<?php

namespace App\Controller;

use App\Enum\RoleGroup;
use App\Service\Deca;
use App\Service\DecaException;
use BackedEnum;
use Cavesman\Db;
use Cavesman\Http\JsonResponse;
use DateTime;
use Exception;

/**
 * Configuración → DeCA: vincular Mollet Express con una empresa de DeCA mediante una clave
 * de su API pública. Permisos: DECA|ACCESS para ver y probar; DECA|EDIT para vincular, cambiar y desvincular.
 *
 * La clave no vuelve nunca al navegador: solo su prefijo.
 */
class DecaConnection
{
    /** Estado de la vinculación. */
    public static function get(): JsonResponse
    {
        if ($denied = self::denied('ACCESS'))
            return $denied;

        return new JsonResponse(self::state(Deca::connection()));
    }

    /**
     * ¿Se pueden crear DeCA? Para appDeCA: basta con estar identificado (no hace falta
     * permisos de DeCA) y no enseña nada de la clave.
     */
    public static function status(): JsonResponse
    {
        try {
            \App\Model\Auth::getEmployee();
        } catch (Exception $e) {
            return new JsonResponse(['message' => 'Token invalido', 'exception' => $e->getMessage()], 401);
        }

        $connection = Deca::connection();

        return new JsonResponse([
            'ready' => Deca::isReady($connection),
            'connected' => $connection !== null,
            'enterprise' => $connection?->enterprise,
            'message' => Deca::notReadyMessage($connection)
        ]);
    }

    /**
     * Vincula (o cambia la dirección/clave). Antes de guardar comprueba con DeCA que la clave
     * vale y tiene los permisos; después localiza o da de alta a Mollet Express como tercero.
     *
     * Body: {url, apiKey}. Sin apiKey y con vinculación previa, se conserva la clave guardada.
     */
    public static function save(): JsonResponse
    {
        if ($denied = self::denied('EDIT'))
            return $denied;

        try {
            $data = json_decode(file_get_contents('php://input'), true) ?: [];
            $connection = Deca::connection();

            $url = Deca::normalizeUrl((string)($data['url'] ?? ''));
            $apiKey = trim((string)($data['apiKey'] ?? ''));

            if (!$apiKey && !$connection)
                return new JsonResponse(['message' => 'Pega la clave de API creada en DeCA'], 400);

            if ($apiKey && !str_contains($apiKey, '.'))
                return new JsonResponse(['message' => 'La clave no tiene el formato de DeCA (prefijo.secreto)'], 400);

            $apiKey = $apiKey ?: Deca::decrypt($connection->apiKey);
            $client = new Deca($url, $apiKey);

            $ping = $client->ping();

            // Sin permisos en la clave puede hacer todo lo contratado; con lista, deben estar los nuestros.
            $scopes = $ping['scopes'] ?? [];
            $missing = $scopes ? array_values(array_diff(Deca::SCOPES, $scopes)) : [];

            if ($missing)
                return new JsonResponse(['message' => 'A la clave le faltan permisos en DeCA: ' . Deca::scopesText($missing)], 400);

            $connection ??= new \App\Entity\DecaConnection();
            $sameEnterprise = $connection->enterprise === ($ping['enterprise'] ?? null);

            $connection->url = $url;
            $connection->apiKey = Deca::encrypt($apiKey);
            $connection->keyPrefix = Deca::keyPrefix($apiKey);
            $connection->enterprise = $ping['enterprise'] ?? null;
            $connection->lastCheck = new DateTime();
            $connection->lastError = null;

            // Otra empresa: el tercero elegido era de la anterior
            if (!$sameEnterprise) {
                $connection->ownPartnerId = null;
                $connection->ownPartnerName = null;
            }

            $warning = null;

            if (!$connection->ownPartnerId) {
                try {
                    $partner = $client->ensureOwnPartner();
                    $connection->ownPartnerId = $partner['id'] ?? null;
                    $connection->ownPartnerName = $partner['name'] ?? null;
                } catch (Exception $e) {
                    $warning = 'Vinculado, pero no se ha podido dar de alta Mollet Express en DeCA: ' . $e->getMessage();
                    $connection->lastError = $warning;
                }
            }

            $em = Db::getManager();
            $em->persist($connection);
            $em->flush();

            return new JsonResponse([
                'message' => $warning ?? 'Vinculado con ' . ($connection->enterprise ?? 'DeCA'),
                'warning' => $warning !== null,
                'item' => self::state($connection)
            ]);
        } catch (DecaException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    /** Vuelve a comprobar la vinculación guardada contra DeCA. */
    public static function test(): JsonResponse
    {
        if ($denied = self::denied('ACCESS'))
            return $denied;

        $connection = Deca::connection();

        if (!$connection)
            return new JsonResponse(['message' => 'No hay vinculación con DeCA'], 404);

        try {
            $ping = Deca::fromConnection($connection)->ping();
            $connection->enterprise = $ping['enterprise'] ?? $connection->enterprise;
            $connection->lastError = null;
            $message = 'Conexión correcta con ' . ($connection->enterprise ?? 'DeCA');
            $ok = true;
        } catch (Exception $e) {
            $connection->lastError = $e->getMessage();
            $message = $e->getMessage();
            $ok = false;
        }

        $connection->lastCheck = new DateTime();
        $em = Db::getManager();
        $em->persist($connection);
        $em->flush();

        return new JsonResponse(['message' => $message, 'item' => self::state($connection)], $ok ? 200 : 400);
    }

    /** Terceros de la empresa en DeCA, para elegir cuál es Mollet Express. */
    public static function partners(): JsonResponse
    {
        if ($denied = self::denied('ACCESS'))
            return $denied;

        try {
            $client = Deca::fromConnection();

            if (!$client)
                return new JsonResponse(['message' => 'No hay vinculación con DeCA'], 404);

            return new JsonResponse(array_map(fn(array $partner) => [
                'id' => $partner['id'] ?? null,
                'name' => $partner['name'] ?? null,
                'nif' => $partner['nif'] ?? null,
                'roles' => $partner['roles'] ?? [],
                'locality' => $partner['locality'] ?? null
            ], $client->partners()));
        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Fija qué tercero de DeCA es Mollet Express.
     *
     * Body: {id} para elegir uno existente, o {create: true} para localizarlo por NIF o darlo
     * de alta con los datos fiscales de la empresa.
     */
    public static function ownPartner(): JsonResponse
    {
        if ($denied = self::denied('EDIT'))
            return $denied;

        try {
            $connection = Deca::connection();
            $client = Deca::fromConnection($connection);

            if (!$client)
                return new JsonResponse(['message' => 'No hay vinculación con DeCA'], 404);

            $data = json_decode(file_get_contents('php://input'), true) ?: [];

            if (!empty($data['create'])) {
                $partner = $client->ensureOwnPartner();
            } else {
                $id = (int)($data['id'] ?? 0);
                $partner = array_find($client->partners(), fn(array $p) => (int)($p['id'] ?? 0) === $id);

                if (!$partner)
                    return new JsonResponse(['message' => 'Ese tercero no existe en DeCA'], 404);
            }

            $connection->ownPartnerId = $partner['id'] ?? null;
            $connection->ownPartnerName = $partner['name'] ?? null;
            $connection->lastError = null;

            $em = Db::getManager();
            $em->persist($connection);
            $em->flush();

            return new JsonResponse(['message' => 'Mollet Express en DeCA: ' . $connection->ownPartnerName, 'item' => self::state($connection)]);
        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        }
    }

    /** Cuántos clientes (activos o no) están vinculados en DeCA, cuántos han cambiado y cuántos faltan. */
    public static function clients(): JsonResponse
    {
        if ($denied = self::denied('ACCESS'))
            return $denied;

        try {
            return new JsonResponse(Deca::clientsStatus());
        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Vincula los clientes como terceros de DeCA: crea los que no existen y actualiza los que han
     * cambiado. Body: {ids: [..]} para unos concretos; sin ids, todos (los inactivos, como inactivos).
     */
    public static function syncClients(): JsonResponse
    {
        if ($denied = self::denied('EDIT'))
            return $denied;

        try {
            $client = Deca::fromConnection();

            if (!$client)
                return new JsonResponse(['message' => 'No hay vinculación con DeCA'], 404);

            set_time_limit(300);

            $data = json_decode(file_get_contents('php://input'), true) ?: [];
            $ids = array_values(array_filter(array_map('intval', (array)($data['ids'] ?? []))));

            // Los inactivos también: van a DeCA como inactivos
            $criteria = ['deletedOn' => null];
            if ($ids)
                $criteria['id'] = $ids;

            $clients = \App\Entity\Client::findBy($criteria, ['name' => 'ASC']);

            if (!$clients)
                return new JsonResponse(['message' => 'No hay clientes que vincular'], 400);

            $result = $client->syncClients($clients);

            // Se guarda lo vinculado aunque algunos hayan fallado
            Db::getManager()->flush();

            $parts = array_filter([
                $result['created'] ? $result['created'] . ' creados' : null,
                $result['updated'] ? $result['updated'] . ' actualizados' : null,
                $result['unchanged'] ? $result['unchanged'] . ' sin cambios' : null,
                $result['errors'] ? count($result['errors']) . ' con error' : null
            ]);

            return new JsonResponse([
                'message' => 'Clientes en DeCA: ' . implode(', ', $parts),
                'result' => $result,
                'status' => Deca::clientsStatus()
            ]);
        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        }
    }

    /** Desvincula. La clave sigue existiendo en DeCA: allí se revoca. */
    public static function delete(): JsonResponse
    {
        if ($denied = self::denied('EDIT'))
            return $denied;

        try {
            $connection = Deca::connection();

            if ($connection) {
                $connection->delete();
                $em = Db::getManager();
                $em->persist($connection);
                $em->flush();
            }

            return new JsonResponse(['message' => 'Desvinculado de DeCA. Recuerda revocar la clave en DeCA si ya no se usa.', 'item' => self::state(null)]);
        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    /** Lo que ve la pantalla: nunca la clave, solo su prefijo. */
    private static function state(?\App\Entity\DecaConnection $connection): array
    {
        return [
            'connected' => $connection !== null,
            'url' => $connection?->url,
            'keyPrefix' => $connection?->keyPrefix,
            'enterprise' => $connection?->enterprise,
            'ownPartnerId' => $connection?->ownPartnerId,
            'ownPartnerName' => $connection?->ownPartnerName,
            'lastCheck' => $connection?->lastCheck?->format('Y-m-d H:i:s'),
            'lastError' => $connection?->lastError,
            'scopes' => Deca::scopeLabels(),
            'company' => Deca::ownCompany()
        ];
    }

    /**
     * null si tiene el permiso DECA|$role (ACCESS para ver, EDIT para cambiar); si no, la
     * respuesta de error.
     */
    private static function denied(string $role): ?JsonResponse
    {
        try {
            $employee = \App\Model\Auth::getEmployee();
        } catch (Exception $e) {
            return new JsonResponse(['message' => 'Token invalido', 'exception' => $e->getMessage()], 401);
        }

        foreach ($employee->roles as $granted) {
            $name = $granted->role instanceof BackedEnum ? $granted->role->value : $granted->role;
            $group = $granted->group instanceof BackedEnum ? $granted->group->value : $granted->group;

            if ($granted->active && $name === $role && $group === RoleGroup::DECA->value)
                return null;
        }

        return new JsonResponse([
            'message' => $role === 'EDIT'
                ? 'No tienes permiso para cambiar la vinculación con DeCA (DeCA → Editar)'
                : 'No tienes permiso para ver la configuración de DeCA (DeCA → Acceso)'
        ], 403);
    }
}
