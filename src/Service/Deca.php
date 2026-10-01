<?php

namespace App\Service;

use App\Entity\DecaConnection;
use Cavesman\Config;
use Exception;

/**
 * Cliente de la API pública de DeCA (`/api/public/v1`, ver deca/docs/api-publica.md).
 *
 * La empresa de DeCA sale de la clave: no hay ningún parámetro que la indique.
 */
class Deca
{
    const string PUBLIC_PATH = '/api/public/v1';

    /** Permisos que necesita la clave para lo que hace Mollet Express. */
    const array SCOPES = ['PARTNER|ACCESS', 'PARTNER|ADD', 'DECA|ACCESS', 'DECA|ADD'];

    /** Cómo se llaman en la pantalla de claves de DeCA (deca/admin, i18n es): sección → permiso. */
    const array SCOPE_LABELS = [
        'PARTNER|ACCESS' => ['Terceros', 'Ver'],
        'PARTNER|ADD' => ['Terceros', 'Crear'],
        'DECA|ACCESS' => ['Documentos DeCA', 'Ver'],
        'DECA|ADD' => ['Documentos DeCA', 'Crear']
    ];

    /**
     * Permisos agrupados por sección con los nombres de DeCA.
     *
     * @return array<int, array{group: string, roles: string[]}>
     */
    public static function scopeLabels(array $scopes = self::SCOPES): array
    {
        $groups = [];

        foreach ($scopes as $scope) {
            [$group, $role] = self::SCOPE_LABELS[$scope] ?? [$scope, ''];
            $groups[$group][] = $role;
        }

        return array_map(fn($group, $roles) => ['group' => $group, 'roles' => array_values(array_filter($roles))], array_keys($groups), $groups);
    }

    /** «Terceros: Ver y Crear; Documentos DeCA: Ver y Crear» */
    public static function scopesText(array $scopes = self::SCOPES): string
    {
        return implode('; ', array_map(
            fn(array $g) => $g['group'] . ($g['roles'] ? ': ' . implode(' y ', $g['roles']) : ''),
            self::scopeLabels($scopes)
        ));
    }

    public function __construct(
        private readonly string $url,
        private readonly string $apiKey
    )
    {
    }

    /** La vinculación activa, o null si no hay. */
    public static function connection(): ?DecaConnection
    {
        return DecaConnection::findOneBy(['deletedOn' => null], ['id' => 'DESC']);
    }

    /** Vinculado y con Mollet Express elegido como tercero: se pueden crear borradores. */
    public static function isReady(?DecaConnection $connection = null): bool
    {
        $connection ??= self::connection();

        return $connection !== null && $connection->ownPartnerId !== null;
    }

    /** Por qué no se pueden crear DeCA, o null si se puede. */
    public static function notReadyMessage(?DecaConnection $connection = null): ?string
    {
        $connection ??= self::connection();

        if (!$connection)
            return 'Mollet Express no está vinculado con DeCA. Un administrador debe configurarlo en app → DeCA.';

        if ($connection->ownPartnerId === null)
            return 'Falta elegir qué tercero de DeCA es Mollet Express. Un administrador debe hacerlo en app → DeCA.';

        return null;
    }

    /** Cliente con la vinculación activa, o null si no hay. */
    public static function fromConnection(?DecaConnection $connection = null): ?self
    {
        $connection ??= self::connection();

        if (!$connection)
            return null;

        return new self($connection->url, self::decrypt($connection->apiKey));
    }

    /**
     * Deja la dirección como base: sin barra final ni `/api/public/v1` si se ha pegado entera.
     *
     * @throws Exception si no es una dirección http(s)
     */
    public static function normalizeUrl(string $url): string
    {
        $url = rtrim(trim($url), '/');

        if (str_ends_with($url, self::PUBLIC_PATH))
            $url = substr($url, 0, -strlen(self::PUBLIC_PATH));

        if (!preg_match('#^https?://[^\s/]+#i', $url))
            throw new Exception('La dirección de DeCA debe empezar por http:// o https://');

        if (self::isOwnApi($url))
            throw new Exception('Esa es la dirección de la API de Mollet Express, no la de DeCA. Pon la de la API de DeCA'
                . ' (en local, http://127.0.0.1:8099; en producción, el dominio de DeCA).');

        return $url;
    }

    /**
     * ¿Apunta a esta misma API? Llamarse a sí misma no tiene sentido y, con el servidor de
     * desarrollo de PHP (un solo hilo), se queda esperando hasta agotar el tiempo.
     */
    private static function isOwnApi(string $url): bool
    {
        $target = parse_url($url);
        $ownHost = $_SERVER['HTTP_HOST'] ?? null;

        if (!$ownHost || empty($target['host']))
            return false;

        $scheme = strtolower($target['scheme'] ?? 'http');
        $targetPort = $target['port'] ?? ($scheme === 'https' ? 443 : 80);

        $own = parse_url('//' . $ownHost);
        $ownPort = $own['port'] ?? (int)($_SERVER['SERVER_PORT'] ?? 80);

        $local = fn(string $host) => in_array(strtolower($host), ['localhost', '127.0.0.1', '::1', '[::1]'], true) ? 'localhost' : strtolower($host);

        return $local($target['host']) === $local($own['host'] ?? '') && (int)$targetPort === (int)$ownPort;
    }

    /** Prefijo público de la clave (`dk_xxxx` de `dk_xxxx.secreto`). */
    public static function keyPrefix(string $apiKey): string
    {
        return explode('.', $apiKey, 2)[0];
    }

    // ---------------------------------------------------------------- Llamadas a DeCA

    /** @return array{enterprise: ?string, key: ?string, scopes: ?array} */
    public function ping(): array
    {
        return $this->request('GET', '/ping');
    }

    /** Terceros de la empresa en DeCA. */
    public function partners(): array
    {
        return $this->request('GET', '/partners');
    }

    /** Alta o actualización de un tercero (idempotente por NIF en DeCA). Devuelve el tercero. */
    public function savePartner(array $partner): array
    {
        return $this->request('POST', '/partners', $partner)['item'] ?? [];
    }

    /**
     * Da de alta (o actualiza) un tercero sin quitarle los papeles que ya tenga: en DeCA,
     * `roles` sustituye la lista entera.
     */
    public function upsertPartner(array $partner, array $roles): array
    {
        $existing = null;

        if (!empty($partner['nif'])) {
            $existing = $this->findPartnerByNif($partner['nif']);
        } elseif (!empty($partner['reference'])) {
            // Sin NIF, DeCA no puede deduplicar (crearía uno nuevo cada vez): se reutiliza por referencia
            $existing = array_find($this->partners(), fn(array $p) => ($p['reference'] ?? null) === $partner['reference']);

            // (volver a guardarlo sin NIF crearía un duplicado, así que se usa tal cual)
            if ($existing)
                return $existing;
        }

        $partner['roles'] = array_values(array_unique(array_merge($existing['roles'] ?? [], $roles)));

        return $this->savePartner($partner);
    }

    public function findPartnerByNif(string $nif): ?array
    {
        $nif = self::normalizeNif($nif);

        foreach ($this->partners() as $partner)
            if (!empty($partner['nif']) && self::normalizeNif($partner['nif']) === $nif)
                return $partner;

        return null;
    }

    /** Documentos con esa referencia del ERP (el número de albarán). */
    public function findDecaByReference(string $reference): array
    {
        return $this->request('GET', '/deca?reference=' . rawurlencode($reference));
    }

    /** El DeCA vigente (no anulado) con esa referencia, o null si no hay ninguno. */
    public function currentByReference(string $reference): ?array
    {
        foreach ($this->findDecaByReference($reference) as $deca)
            if (($deca['status'] ?? null) !== 'CANCELLED')
                return $deca;

        return null;
    }

    /**
     * Igual que {@see currentByReference()}, con la vinculación de la empresa.
     *
     * @throws Exception si no está vinculado o DeCA no responde
     */
    public static function findByReference(string $reference): ?array
    {
        $connection = self::connection();

        if (!self::isReady($connection))
            throw new Exception(self::notReadyMessage($connection));

        return self::fromConnection($connection)->currentByReference($reference);
    }

    /** Crea un borrador. Devuelve el documento. */
    public function createDraft(array $deca): array
    {
        return $this->request('POST', '/deca', $deca)['item'] ?? [];
    }

    /**
     * Comparte el borrador con un tercero para que lo rellene en DeCA Cliente y, con `notify`,
     * DeCA le manda el aviso a los correos de su ficha y de sus contactos.
     *
     * @return array{message: string, item: array, notified: array<int, array{email: string, sent: bool}>}
     */
    public function share(int $decaId, int $partnerId, bool $notify = true): array
    {
        return $this->request('POST', '/deca/' . $decaId . '/share', ['partner' => $partnerId, 'notify' => $notify]);
    }

    /**
     * Tercero de DeCA que es la propia Mollet Express: el que tenga su NIF o, si no existe,
     * uno nuevo con los datos fiscales de la empresa (modules.factura.empresa).
     *
     * Papeles: transportista (transportista efectivo) y cargador contractual.
     */
    public function ensureOwnPartner(): array
    {
        $company = self::ownCompany();

        if (empty($company['nif']))
            throw new Exception('Falta el NIF de la empresa en la configuración (modules.factura.empresa.nif)');

        return $this->upsertPartner($company, ['CARRIER', 'SHIPPER']);
    }

    /**
     * Crea en DeCA el borrador de un albarán de appDeCA y deja el resultado en el propio
     * albarán (decaId / decaStatus / decaError). No hace flush: lo guarda quien llama.
     *
     * Solo se pone a Mollet Express, según el tipo del albarán:
     * - TRANSPORTISTA_EFECTIVO: transportista = Mollet Express.
     * - CARGADOR_CONTRACTUAL: cargador = Mollet Express.
     * El cliente NO se vincula: el resto del documento lo completa el cliente.
     *
     * La fecha del transporte es la indicada en appDeCA; si no viene, la del albarán.
     *
     * Idempotente: si ya hay un documento en DeCA con la referencia del albarán (su número),
     * se vincula ese en vez de crear otro. Nunca lanza: un fallo de DeCA no debe perder el albarán.
     */
    public static function syncAlbaran(\App\Entity\Document\Albaran\Albaran $albaran, ?\DateTimeInterface $transportDate = null): void
    {
        if ($albaran->transportRole === null || $albaran->decaId !== null)
            return;

        try {
            $connection = self::connection();

            if (!self::isReady($connection))
                throw new Exception(self::notReadyMessage($connection));

            if (!$albaran->number)
                throw new Exception('El albarán no tiene número: es la referencia del DeCA');

            $client = self::fromConnection($connection);

            // Ya creado (un reintento tras un corte a medias, o hecho en DeCA): se enlaza ese
            if ($existing = $client->currentByReference($albaran->number)) {
                $deca = $existing;
            } else {
                $own = $connection->ownPartnerId;
                $isCarrier = $albaran->transportRole === 'TRANSPORTISTA_EFECTIVO';

                $payload = array_filter([
                    'reference' => $albaran->number,
                    'transportDate' => ($transportDate ?? $albaran->date ?? new \DateTime())->format('Y-m-d'),
                    'shipper' => $isCarrier ? null : $own,
                    'carrier' => $isCarrier ? $own : null,
                    'notes' => 'Albarán ' . $albaran->number . ' de Mollet Express'
                ], fn($value) => $value !== null);

                $deca = $client->createDraft($payload);
            }

            if (empty($deca['id']))
                throw new Exception('DeCA no ha devuelto el documento creado');

            $albaran->decaId = (int)$deca['id'];
            $albaran->decaStatus = 'CREATED';
            $albaran->decaError = null;
        } catch (Exception $e) {
            $albaran->decaStatus = 'ERROR';
            $albaran->decaError = $e->getMessage();
        }
    }

    /**
     * Comparte el borrador del albarán con el cliente para que lo rellene en DeCA Cliente, y
     * DeCA le avisa por correo. El cliente entra con el correo de su ficha (que viaja a DeCA
     * al vincularlo como tercero) y un código.
     *
     * Si el cliente aún no está en DeCA, o sus datos han cambiado, se vincula antes. El cliente
     * no pasa a ser parte del documento: solo es con quien se comparte.
     *
     * Resultado en el albarán: decaStatus = SHARED si se ha compartido; si no ha salido el
     * aviso, SHARED igualmente con el motivo en decaError. Si no se ha podido compartir, sigue
     * CREATED con el motivo. No hace flush y nunca lanza.
     */
    public static function shareAlbaran(\App\Entity\Document\Albaran\Albaran $albaran): void
    {
        if ($albaran->decaId === null)
            return;

        try {
            $connection = self::connection();

            if (!self::isReady($connection))
                throw new Exception(self::notReadyMessage($connection));

            $customer = $albaran->client;

            if (!$customer)
                throw new Exception('El albarán no tiene cliente');

            $deca = self::fromConnection($connection);

            if (!$customer->decaPartnerId || $customer->decaHash !== self::clientHash(self::clientPayload($customer))) {
                $result = $deca->syncClients([$customer]);

                if ($result['errors'])
                    throw new Exception('no se ha podido vincular el cliente en DeCA: ' . $result['errors'][0]['message']);
            }

            $response = $deca->share($albaran->decaId, (int)$customer->decaPartnerId);
            $sent = array_column(array_filter($response['notified'] ?? [], fn(array $r) => !empty($r['sent'])), 'email');

            $albaran->decaStatus = 'SHARED';
            $albaran->decaError = $sent ? null : match ($response['message'] ?? '') {
                'deca.share.no-email' => 'Compartido, pero el cliente no tiene correo en su ficha: no podrá entrar. Añádeselo y vuelve a compartir.',
                default => 'Compartido, pero DeCA no ha podido enviar el correo de aviso al cliente.'
            };
        } catch (Exception $e) {
            $albaran->decaStatus = 'CREATED';
            $albaran->decaError = 'No se ha compartido con el cliente: ' . $e->getMessage();
        }
    }

    /** Papeles con los que se dan de alta los clientes en DeCA (los que ya tengan allí se conservan). */
    const array CLIENT_ROLES = ['SHIPPER', 'CONSIGNEE'];

    /** Un cliente de Mollet Express en el formato de tercero de DeCA. */
    public static function clientPayload(\App\Entity\Client $client): array
    {
        return array_filter([
            'name' => trim((string)$client->name),
            'nif' => trim((string)$client->nif),
            'reference' => 'MOLLET-CLIENTE-' . $client->id,
            'address' => $client->direccion,
            'postalCode' => (string)($client->codigoPostal ?? ''),
            'locality' => $client->localidad,
            'state' => $client->provincia,
            'email' => $client->email,
            'phone' => $client->telefono ?: $client->movil,
            'country' => 'ES'
        ], fn($value) => $value !== null && $value !== '');
    }

    /** Huella de los datos que se mandan: si no cambia, no hace falta volver a enviarlos. */
    public static function clientHash(array $payload): string
    {
        ksort($payload);

        return sha1(json_encode($payload, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Da de alta o actualiza en DeCA los clientes indicados como terceros y deja el vínculo en
     * cada cliente (decaPartnerId, decaHash, decaSyncedAt). Hace persist de cada cliente; el flush, quien llama.
     *
     * - No existe (ni por NIF ni por la referencia MOLLET-CLIENTE-<id>): se crea.
     * - Existe y los datos han cambiado desde el último envío (o nunca se vinculó): se actualiza,
     *   conservando los papeles que tenga en DeCA.
     * - Existe y no ha cambiado nada: no se toca.
     * - Sin NIF: DeCA no puede actualizarlo sin duplicarlo; se crea una vez y después solo se vincula.
     *
     * @param \App\Entity\Client[] $clients
     * @return array{created: int, updated: int, unchanged: int, warnings: array, errors: array}
     */
    public function syncClients(array $clients): array
    {
        $result = ['created' => 0, 'updated' => 0, 'unchanged' => 0, 'warnings' => [], 'errors' => []];

        // Una sola lectura de los terceros de DeCA para todo el lote
        $byNif = [];
        $byReference = [];

        foreach ($this->partners() as $partner) {
            if (!empty($partner['nif']))
                $byNif[self::normalizeNif($partner['nif'])] ??= $partner;
            if (!empty($partner['reference']))
                $byReference[$partner['reference']] ??= $partner;
        }

        foreach ($clients as $client) {
            try {
                $payload = self::clientPayload($client);
                $hash = self::clientHash($payload);

                if (empty($payload['name']))
                    throw new Exception('no tiene nombre');

                $hasNif = !empty($payload['nif']);
                $existing = ($hasNif ? $byNif[self::normalizeNif($payload['nif'])] ?? null : null)
                    ?? $byReference[$payload['reference']] ?? null;

                if ($existing && (int)$existing['id'] === $client->decaPartnerId && $client->decaHash === $hash) {
                    $result['unchanged']++;
                    continue;
                }

                if ($existing && !$hasNif) {
                    // Actualizarlo sin NIF crearía otro: se vincula el que hay
                    $partner = $existing;
                    $result['warnings'][] = ['client' => $client->name, 'message' => 'Sin NIF: vinculado, pero sus datos no se actualizan en DeCA'];
                    $result['unchanged']++;
                } else {
                    $payload['roles'] = $existing ? ($existing['roles'] ?? []) : self::CLIENT_ROLES;
                    $partner = $this->savePartner($payload);
                    $existing ? $result['updated']++ : $result['created']++;

                    if (!$hasNif)
                        $result['warnings'][] = ['client' => $client->name, 'message' => 'Sin NIF: creado en DeCA, pero después no se podrá actualizar'];
                }

                if (empty($partner['id']))
                    throw new Exception('DeCA no ha devuelto el tercero');

                $client->decaPartnerId = (int)$partner['id'];
                $client->decaHash = $hash;
                $client->decaSyncedAt = new \DateTime();

                // Seguimiento de cambios explícito (Cavesman): sin persist, el flush no lo guarda
                \Cavesman\Db::getManager()->persist($client);

                // Para el resto del lote (p. ej. dos clientes con el mismo NIF)
                if ($hasNif)
                    $byNif[self::normalizeNif($payload['nif'])] = $partner + ['nif' => $payload['nif']];
                $byReference[$payload['reference']] ??= $partner;
            } catch (Exception $e) {
                $result['errors'][] = ['client' => $client->name, 'message' => $e->getMessage()];
            }
        }

        return $result;
    }

    /**
     * Cómo están los clientes activos respecto a DeCA, sin llamar a DeCA.
     *
     * @return array{total: int, linked: int, outdated: int, pending: int}
     */
    public static function clientsStatus(): array
    {
        $status = ['total' => 0, 'linked' => 0, 'outdated' => 0, 'pending' => 0];

        foreach (\App\Entity\Client::findBy(['deletedOn' => null, 'active' => true]) as $client) {
            $status['total']++;

            if (!$client->decaPartnerId) {
                $status['pending']++;
            } elseif ($client->decaHash !== self::clientHash(self::clientPayload($client))) {
                $status['outdated']++;
            } else {
                $status['linked']++;
            }
        }

        return $status;
    }

    /** Datos fiscales de Mollet Express, en el formato de tercero de DeCA. */
    public static function ownCompany(): array
    {
        $name = trim(implode(' ', array_filter([
            Config::get('modules.factura.empresa.nombre_fiscal'),
            Config::get('modules.factura.empresa.nombre_fiscal2')
        ])));

        return array_filter([
            'name' => $name ?: 'Mollet Express',
            'nif' => Config::get('modules.factura.empresa.nif'),
            'address' => Config::get('modules.factura.empresa.direccion'),
            'postalCode' => (string)Config::get('modules.factura.empresa.cp', ''),
            'locality' => Config::get('modules.factura.empresa.localidad'),
            'state' => Config::get('modules.factura.empresa.provincia'),
            'country' => 'ES'
        ], fn($value) => $value !== null && $value !== '');
    }

    // ---------------------------------------------------------------- HTTP

    /**
     * @throws DecaException con un mensaje legible y el código HTTP de DeCA
     */
    private function request(string $method, string $path, ?array $body = null): array
    {
        $curl = curl_init($this->url . self::PUBLIC_PATH . $path);

        $headers = ['X-Api-Key: ' . $this->apiKey, 'Accept: application/json'];

        if ($body !== null) {
            $headers[] = 'Content-Type: application/json; charset=utf-8';
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body, JSON_UNESCAPED_UNICODE));
        }

        curl_setopt_array($curl, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 15
        ]);

        $response = curl_exec($curl);
        $status = (int)curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $errno = curl_errno($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($response === false)
            throw new DecaException(match ($errno) {
                CURLE_OPERATION_TIMEDOUT => 'DeCA no responde en ' . $this->url . ' (tiempo agotado). Comprueba que sea la dirección'
                    . ' de la API de DeCA y que esté en marcha.',
                CURLE_COULDNT_CONNECT => 'No hay nada escuchando en ' . $this->url . '. Comprueba la dirección y el puerto de la'
                    . ' API de DeCA y que esté en marcha.',
                CURLE_COULDNT_RESOLVE_HOST => 'No se encuentra el servidor ' . $this->url . '. Revisa la dirección.',
                default => 'No se ha podido conectar con DeCA (' . $this->url . '): ' . $error
            }, 0);

        $data = json_decode($response, true);

        if ($status >= 200 && $status < 300)
            return is_array($data) ? $data : [];

        throw new DecaException(self::errorMessage($status, is_array($data) ? $data : []), $status, $data);
    }

    private static function errorMessage(int $status, array $data): string
    {
        return match ($status) {
            401 => 'DeCA no acepta la clave: no existe, está caducada o revocada, o la empresa está desactivada',
            403 => 'La clave de DeCA no tiene el permiso necesario'
                . (!empty($data['role']) && isset(self::SCOPE_LABELS[$data['role']]) ? ' (' . self::scopesText([$data['role']]) . ')' : '')
                . '. Debe tener: ' . self::scopesText(),
            402 => 'Se ha agotado el tope de documentos del plan de DeCA en este periodo',
            404 => 'DeCA responde que no existe (404): si es al vincular, revisa la dirección',
            default => 'DeCA ha respondido ' . $status . (!empty($data['message']) ? ': ' . $data['message'] : '')
                . (!empty($data['exception']) ? ' (' . $data['exception'] . ')' : '')
        };
    }

    private static function normalizeNif(string $nif): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $nif));
    }

    // ---------------------------------------------------------------- Cifrado de la clave

    /** Cifra la clave de DeCA para guardarla (AES-256-GCM con la clave de la API). */
    public static function encrypt(string $plain): string
    {
        $iv = random_bytes(12);
        $cipher = openssl_encrypt($plain, 'aes-256-gcm', self::secret(), OPENSSL_RAW_DATA, $iv, $tag);

        return base64_encode($iv . $tag . $cipher);
    }

    /**
     * @throws Exception si no se puede descifrar (p. ej. ha cambiado api.key): hay que volver a vincular
     */
    public static function decrypt(string $stored): string
    {
        $raw = base64_decode($stored, true);

        $plain = $raw !== false && strlen($raw) > 28
            ? openssl_decrypt(substr($raw, 28), 'aes-256-gcm', self::secret(), OPENSSL_RAW_DATA, substr($raw, 0, 12), substr($raw, 12, 16))
            : false;

        if ($plain === false)
            throw new Exception('No se puede leer la clave de DeCA guardada: vuelve a vincular');

        return $plain;
    }

    private static function secret(): string
    {
        return hash('sha256', 'deca-connection|' . Config::get('api.key', ''), true);
    }
}
