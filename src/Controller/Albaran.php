<?php

namespace App\Controller;

use App\Entity\Document\Albaran\AlbaranLinea;
use App\Entity\Document\Factura\FacturaLinea;
use App\Enum\DocumentStatus;
use App\Model\DataTable;
use App\Model\FacturarIntervalDates;
use App\Model\MultiClientIntervalDates;
use App\Model\Pdf\DefaultPdf;
use App\Model\Pdf\FacturaPdf;
use App\Model\Pdf\ListadoPdf;
use Cavesman\Config;
use Cavesman\Db;
use Cavesman\Enum\Directory;
use Cavesman\FileSystem;
use Cavesman\Http;
use Cavesman\Mail;
use Cavesman\Request;
use DateInterval;
use DateTime;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use ReflectionClass;
use ZipArchive;

class Albaran
{
    public static array $config = [];

    public static function filter(): Http\JsonResponse
    {
        try {
            $em = Db::getManager();

            $qb = $em->getRepository(\App\Entity\Document\Albaran\Albaran::class)
                ->createQueryBuilder('i')
                ->leftJoin('i.client', 'c')
                ->where('i.deletedOn IS NULL');

            $filter = json_decode(Request::get('filter', '[]'));

            if ($filter && $filter->search) {
                foreach (explode(' ', $filter->search->value) as $key => $string) {
                    $qb
                        ->andWhere('i.serie LIKE :search_' . $key . ' OR i.number LIKE :search_' . $key
                            . ' OR i.observaciones LIKE :search_' . $key . ' OR i.tax LIKE :search_' . $key
                            . ' OR i.comments LIKE :search_' . $key . ' OR c.name LIKE :search_' . $key
                        )
                        ->setParameter('search_' . $key, '%' . $string . '%');
                }
            }

            if (!empty($filter->minDate)) {
                $qb->andWhere('i.date >= :minDate')
                    ->setParameter('minDate', new DateTime($filter->minDate));
            }

            if (!empty($filter->maxDate)) {
                $maxDate = new DateTime($filter->maxDate);
                $maxDate->setTime(23, 59, 59);

                $qb->andWhere('i.date <= :maxDate')
                    ->setParameter('maxDate', $maxDate);
            }

            if (!empty($filter->clientId)) {
                $qb->andWhere('c.id = :clientId')
                    ->setParameter('clientId', $filter->clientId);
            }

            $total = clone $qb;
            $total->select('COUNT(i.id)');
            $recordsTotal = (int)$total->getQuery()->getSingleScalarResult();

            $sumQb = clone $qb;
            $sumQb->select('SUM(i.importeBruto)');
            $totalImporteBruto = (float)$sumQb->getQuery()->getSingleScalarResult();

            $recentIds = $filter->recentIds ?? [];

            if (!empty($recentIds)) {
                $qb->addSelect('CASE WHEN i.id IN (:recentIds) THEN 0 ELSE 1 END AS HIDDEN priority')
                    ->setParameter('recentIds', $recentIds);

                $qb->addOrderBy('priority', 'ASC');
            }

            if ($filter->order && $filter->columns) {
                foreach ($filter->order as $order) {
                    $index = $order->column;
                    $columnName = $filter->columns[$index]->data;
                    $dir = strtoupper($order->dir);
                    if ($dir === 'ASC' || $dir === 'DESC') {
                        if (str_starts_with($columnName, 'client.')) {
                            $field = substr($columnName, strlen('client.'));
                            $qb->addOrderBy('c.' . $field, $dir);
                        } else
                            $qb->addOrderBy('i.' . $columnName, $dir);

                    }
                }
            }

            if ($filter->length ?? false) {
                $qb->setMaxResults($filter->length)
                    ->setFirstResult($filter->start);
            }

            /** @var \App\Entity\Document\Albaran\Albaran $list */
            $list = $qb->getQuery()->getResult();

            $datatable = new DataTable();
            $datatable->recordsTotal = $recordsTotal;
            $datatable->recordsFiltered = $recordsTotal;
            $datatable->totalImporteBruto = $totalImporteBruto;

            /** @var \App\Entity\Document\Albaran\Albaran $item */
            foreach ($list as $item) {
                if ($item->factura && $item->factura->ordenCobro)
                    $item->factura->ordenCobro = null;
                /** @var \App\Model\Document\Albaran\Albaran $model */
                $model = $item->model(\App\Model\Document\Albaran\Albaran::class);
                $datatable->data[] = $model->json();
            }

            return new  Http\JsonResponse($datatable);
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }

    }


    public static function get(int $id): Http\JsonResponse
    {
        try {

            $item = \App\Entity\Document\Albaran\Albaran::findOneBy(['id' => $id, 'deletedOn' => null]);

            return new Http\JsonResponse($item->model(\App\Model\Document\Albaran\Albaran::class)->json());
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function add(): Http\JsonResponse
    {
        try {

            $model = \App\Model\Document\Albaran\Albaran::fromRequest();
            $model->validateTransportRole();

            // Los albaranes de appDeCA (llevan tipo de transporte) generan su DeCA: sin vinculación no se crean
            if ($model->transportRole !== null && !\App\Service\Deca::isReady())
                return new Http\JsonResponse(['message' => \App\Service\Deca::notReadyMessage()], 409);

            // appDeCA con un número de albarán que ya existe: no se duplica
            // Fecha del transporte y si se comparte con el cliente: solo para el DeCA
            $options = self::decaOptions();

            if ($model->transportRole !== null && ($linked = self::linkExisting($model, $options)))
                return $linked;

            /** @var \App\Entity\Document\Albaran\Albaran $entity */
            $entity = $model->entity();

            $em = DB::getManager();

            foreach ($entity->lineas as $linea) {
                $linea->albaran = $entity;
            }

            // El estado del DeCA lo pone la API, no quien llama
            $entity->decaId = null;
            $entity->decaStatus = null;
            $entity->decaError = null;

            $em->persist($entity);
            $em->flush();

            // Albarán de appDeCA: se crea su borrador en DeCA. Si falla, el albarán queda
            // guardado con decaStatus = ERROR y se puede reintentar (POST /{id}/deca).
            // Y, si así se ha pedido, se comparte con el cliente para que lo rellene en DeCA
            // Cliente; si no, queda sin compartir y se comparte a mano desde la web de DeCA.
            if ($entity->transportRole !== null) {
                \App\Service\Deca::syncAlbaran($entity, $options['transportDate']);
                if ($options['share'])
                    \App\Service\Deca::shareAlbaran($entity);
                // Seguimiento de cambios explícito (Cavesman): sin persist, flush no guarda
                $em->persist($entity);
                $em->flush();
            }

            return new Http\JsonResponse([
                'message' => "Albarán añadido correctamente",
                'item' => $entity->model(\App\Model\Document\Albaran\Albaran::class)->json(),
                'deca' => self::decaResult($entity, $options['share'])
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Hace lo que le falte al DeCA de un albarán de appDeCA: crear el borrador si falló y
     * compartirlo con el cliente. Si ya estaba compartido, lo vuelve a compartir, que manda
     * otra vez el aviso por correo al cliente.
     *
     * Opcional en el cuerpo: `transportDate` (Y-m-d, si se crea el borrador) y `share`
     * (false: no se comparte; queda para compartirlo a mano desde la web de DeCA).
     */
    public static function deca(int $id): Http\JsonResponse
    {
        try {
            \App\Model\Auth::getEmployee();
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => 'Token invalido', 'exception' => $e->getMessage()], 401);
        }

        try {
            $entity = \App\Entity\Document\Albaran\Albaran::findOneBy(['id' => $id, 'deletedOn' => null]);

            if (!$entity)
                return new Http\JsonResponse(['message' => "Albarán no encontrado"], 404);

            // appDeCA vinculando un albarán que ya existía: lo que le falte (tipo, cliente) sale
            // de lo que manda; lo que ya tenga no se toca.
            $body = json_decode((string)file_get_contents('php://input'), true);
            $options = self::decaOptions();

            if (is_array($body))
                self::fillForDeca($entity, $body['transportRole'] ?? null, (int)($body['client']['id'] ?? $body['client'] ?? 0));

            if ($entity->transportRole === null)
                return new Http\JsonResponse(['message' => "Este albarán no es de appDeCA: no lleva DeCA"], 400);

            \App\Service\Deca::syncAlbaran($entity, $options['transportDate']);
            if ($options['share'])
                \App\Service\Deca::shareAlbaran($entity);
            // Seguimiento de cambios explícito (Cavesman): sin persist, flush no guarda
            $em = DB::getManager();
            $em->persist($entity);
            $em->flush();

            $result = self::decaResult($entity, $options['share']);

            return new Http\JsonResponse([
                'message' => $result['message'],
                'item' => $entity->model(\App\Model\Document\Albaran\Albaran::class)->json(),
                'deca' => $result
            ], $result['created'] ? 200 : 502);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * appDeCA con el número de un albarán que ya existe:
     * - El albarán ya tiene DeCA (vinculado, o uno en DeCA con esa referencia): no se hace
     *   nada y se avisa (409).
     * - No tiene DeCA: no se crea otro albarán; se crea el DeCA del existente y se comparte
     *   con su cliente (salvo que se pida no compartirlo). Si le falta el tipo o el cliente, se toman de lo que manda appDeCA.
     *
     * Si el albarán no existe devuelve null y se sigue con el alta normal (que, si ya hay un
     * DeCA con esa referencia, lo vincula en vez de crear otro).
     */
    private static function linkExisting(\App\Model\Document\Albaran\Albaran $model, array $options): ?Http\JsonResponse
    {
        $number = trim((string)$model->number);

        if ($number === '')
            return null;

        try {
            ['albaran' => $existing, 'deca' => $deca] = self::existingFor($number);
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => 'No se ha podido comprobar en DeCA si el albarán ' . $number . ' ya tiene documento: ' . $e->getMessage()], 502);
        }

        if (!$existing)
            return null;

        if ($deca)
            return new Http\JsonResponse([
                'message' => 'Ya existen el albarán ' . $number . ' y su DeCA (id ' . $deca['id'] . '): no se ha creado nada',
                'exists' => true
            ], 409);

        self::fillForDeca($existing, $model->transportRole, (int)($model->client?->id ?? 0));

        \App\Service\Deca::syncAlbaran($existing, $options['transportDate']);
        if ($options['share'])
            \App\Service\Deca::shareAlbaran($existing);

        // Seguimiento de cambios explícito (Cavesman): sin persist, flush no guarda
        $em = DB::getManager();
        $em->persist($existing);
        $em->flush();

        return new Http\JsonResponse([
            'message' => 'El albarán ' . $number . ' ya existía: se ha vinculado con su DeCA',
            'linked' => true,
            'item' => $existing->model(\App\Model\Document\Albaran\Albaran::class)->json(),
            'deca' => self::decaResult($existing, $options['share'])
        ]);
    }

    /**
     * appDeCA, antes de guardar: qué pasará con ese número de albarán.
     *
     * - `add`: no existe el albarán; se creará (y si ya hay un DeCA con esa referencia, se
     *   vinculará a él en vez de crear otro).
     * - `link`: existe el albarán pero no su DeCA; no se crea otro albarán, se le vincula uno.
     * - `exists`: existen el albarán y su DeCA; no se puede hacer nada.
     *
     * GET /delivery-note/deca-check?number=A226-2308
     */
    public static function decaCheck(): Http\JsonResponse
    {
        try {
            \App\Model\Auth::getEmployee();
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => 'Token invalido', 'exception' => $e->getMessage()], 401);
        }

        $number = trim((string)($_GET['number'] ?? ''));

        if ($number === '')
            return new Http\JsonResponse(['message' => 'Indica el número de albarán'], 400);

        try {
            ['albaran' => $albaran, 'deca' => $deca] = self::existingFor($number);
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => 'No se ha podido comprobar en DeCA si el albarán ' . $number . ' ya tiene documento: ' . $e->getMessage()], 502);
        }

        return new Http\JsonResponse([
            'action' => !$albaran ? 'add' : ($deca ? 'exists' : 'link'),
            'albaran' => $albaran ? [
                'id' => $albaran->id,
                'number' => $albaran->number,
                'date' => $albaran->date?->format('Y-m-d'),
                'client' => $albaran->client ? ['id' => $albaran->client->id, 'name' => $albaran->client->name] : null,
                'transportRole' => $albaran->transportRole,
                'decaId' => $albaran->decaId
            ] : null,
            'deca' => $deca ? ['id' => (int)$deca['id'], 'status' => $deca['status'] ?? null] : null
        ]);
    }

    /**
     * El albarán con ese número y su DeCA: el que tiene vinculado o, si no tiene, uno vigente
     * en DeCA con esa referencia. Sin albarán no se pregunta a DeCA: el alta ya vincula el
     * que haya.
     *
     * @return array{albaran: ?\App\Entity\Document\Albaran\Albaran, deca: ?array}
     * @throws Exception si hay que preguntar a DeCA y no responde
     */
    private static function existingFor(string $number): array
    {
        /** @var \App\Entity\Document\Albaran\Albaran|null $albaran */
        $albaran = \App\Entity\Document\Albaran\Albaran::findOneBy(['number' => $number, 'deletedOn' => null]);

        if (!$albaran)
            return ['albaran' => null, 'deca' => null];

        $deca = $albaran->decaId !== null ? ['id' => $albaran->decaId] : \App\Service\Deca::findByReference($number);

        return ['albaran' => $albaran, 'deca' => $deca];
    }

    /** Al vincular un albarán que ya existía: el tipo y el cliente, solo si le faltan. */
    private static function fillForDeca(\App\Entity\Document\Albaran\Albaran $albaran, ?string $transportRole, int $clientId): void
    {
        if ($albaran->transportRole === null && in_array($transportRole, \App\Model\Document\Albaran\Albaran::TRANSPORT_ROLES, true))
            $albaran->transportRole = $transportRole;

        if (!$albaran->client && $clientId)
            $albaran->client = \App\Entity\Client::findOneBy(['id' => $clientId, 'deletedOn' => null]);
    }

    /**
     * Lo que appDeCA manda para el DeCA y no se guarda en el albarán:
     * - `transportDate` (Y-m-d): fecha del transporte del borrador. Sin ella, la del albarán.
     * - `share`: si se comparte con el cliente. Por defecto no: el borrador queda sin
     *   compartir en DeCA. El «Enviar al cliente» de app manda true.
     *
     * @return array{transportDate: ?\DateTime, share: bool}
     * @throws Exception si la fecha no es válida
     */
    private static function decaOptions(): array
    {
        $body = json_decode((string)file_get_contents('php://input'), true);
        $body = is_array($body) ? $body : [];

        $date = trim((string)($body['transportDate'] ?? ''));
        $transportDate = null;

        if ($date !== '') {
            $transportDate = \DateTime::createFromFormat('!Y-m-d', $date);

            if (!$transportDate || $transportDate->format('Y-m-d') !== $date)
                throw new Exception('Fecha del transporte no válida: ' . $date);
        }

        return [
            'transportDate' => $transportDate,
            'share' => filter_var($body['share'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false
        ];
    }

    /** Resumen del DeCA de un albarán para la respuesta. */
    private static function decaResult(\App\Entity\Document\Albaran\Albaran $entity, bool $share = false): ?array
    {
        if ($entity->transportRole === null)
            return null;

        $created = $entity->decaId !== null;
        $shared = $entity->decaStatus === 'SHARED';

        return [
            'created' => $created,
            'shared' => $shared,
            // Algo no ha ido del todo: sin borrador, sin compartir o sin aviso al cliente
            'warning' => !$created || $entity->decaError !== null,
            'id' => $entity->decaId,
            'message' => match (true) {
                !$created => 'El albarán se ha guardado, pero no se ha podido crear el borrador DeCA: ' . $entity->decaError,
                $entity->decaError !== null => 'Borrador DeCA creado (id ' . $entity->decaId . '). ' . $entity->decaError,
                !$share && !$shared => 'Borrador DeCA creado (id ' . $entity->decaId . '). No se ha enviado al cliente: compártelo desde la web de DeCA cuando quieras',
                default => 'Borrador DeCA creado (id ' . $entity->decaId . ') y enviado al cliente para que lo rellene'
            }
        ];
    }

    public static function update(int $id): Http\JsonResponse
    {
        try {
            $item = \App\Entity\Document\Albaran\Albaran::findOneBy(['id' => $id, 'deletedOn' => null]);

            if (!$item)
                return new Http\JsonResponse(['message' => "Albarán no encontrado"], 404);

            $model = \App\Model\Document\Albaran\Albaran::fromRequest();

            if ($id != $model->id)
                return new Http\JsonResponse(['message' => "La id indicada en la url no corresponde a la enviada en el modelo"], 404);

            $model->validateTransportRole();

            $em = DB::getManager();

            $idLineas = array_map(fn($linea) => $linea->id, $model->lineas);

            /** @var AlbaranLinea $linea */
            foreach ($item->lineas as $linea) {
                if (!in_array($linea->id, $idLineas)) {
                    $linea->delete();
                    $em->persist($linea);
                }
            }

            /** @var \App\Entity\Document\Albaran\Albaran $entity */
            $entity = $model->entity();

            if (!empty($entity->number)) {
                $duplicate = $em->getRepository(\App\Entity\Document\Albaran\Albaran::class)
                    ->createQueryBuilder('a')
                    ->where('a.number = :number AND a.id != :id AND a.deletedOn IS NULL')
                    ->setParameter('number', $entity->number)
                    ->setParameter('id', $entity->id)
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult();

                if ($duplicate) {
                    $em->detach($entity);
                    $item = \App\Entity\Document\Albaran\Albaran::findOneBy(['id' => $id, 'deletedOn' => null]);
                    return new Http\JsonResponse([
                        'message' => "El número $entity->number ya está en uso por otro albarán",
                        'item' => $item->model(\App\Model\Document\Albaran\Albaran::class)->json()
                    ], 409);
                }
            }

            foreach ($entity->lineas as $linea) {
                $linea->albaran = $entity;
            }

            if (empty($entity->number))
                $entity->status = DocumentStatus::DRAFT;
            else
                $entity->status = DocumentStatus::ACTIVE;

            $em->persist($entity);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Albarán actualizado correctamente",
                'item' => $entity->model(\App\Model\Document\Albaran\Albaran::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function delete(int $id): Http\JsonResponse
    {
        try {

            $em = DB::getManager();

            $item = \App\Entity\Document\Albaran\Albaran::findOneBy(['id' => $id, 'deletedOn' => null]);

            $item->delete();

            $em->persist($item);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Albarán eliminado correctamente",
                'item' => $item->model(\App\Model\Document\Albaran\Albaran::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function factura(int $id): Http\JsonResponse
    {
        try {

            $item = \App\Entity\Document\Albaran\Albaran::findOneBy(['id' => $id, 'deletedOn' => null]);

            /** @var \App\Model\Document\Albaran\Albaran $albaran */
            $albaran = $item->model(\App\Model\Document\Albaran\Albaran::class);
            $albaran->id = null;

            foreach ($albaran->lineas as $linea) {
                $linea->id = null;
            }

            // TODO: Clonar con json_decode etc

            $array = json_decode(json_encode($albaran->json()), true);

            $factura = new \App\Model\Document\Factura\Factura($array);

            /** @var \App\Entity\Document\Factura\Factura $facturaEntity */
            $facturaEntity = $factura->entity();

            foreach ($facturaEntity->lineas as $linea) {
                $linea->factura = $facturaEntity;
            }

            if (empty($facturaEntity->number))
                $facturaEntity->status = DocumentStatus::DRAFT;
            else
                $facturaEntity->status = DocumentStatus::ACTIVE;

            $em = DB::getManager();
            $em->persist($facturaEntity);
            $em->flush();

            $em = DB::getManager();
            $item->factura = $facturaEntity;
            $em->persist($item);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Factura generada correctamente",
                'item' => $item->model(\App\Model\Document\Albaran\Albaran::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function facturar(): Http\JsonResponse
    {
        try {
            $model = FacturarIntervalDates::fromRequest();

            $dateStart = $model->start instanceof DateTime ? $model->start : new DateTime($model->start);
            $dateEnd = $model->end instanceof DateTime ? $model->end : new DateTime($model->end);
            $dateFactura = $model->dateInvoice instanceof DateTime ? $model->dateInvoice : new DateTime($model->dateInvoice);

            $clients = [];
            foreach ($model->clients as $client) {
                $clients[] = \App\Entity\Client::findOneBy(['id' => $client->id, 'deletedOn' => null]);
            }

            $em = DB::getManager();

            if (file_exists(new ReflectionClass(FacturaPdf::class)->getFileName()))
                require_once new ReflectionClass(FacturaPdf::class)->getFileName();
            else
                require_once new ReflectionClass(DefaultPdf::class)->getFileName();

            $resultItems = $em
                ->createQueryBuilder()
                ->select('o')
                ->from(\App\Entity\Document\Albaran\Albaran::class, "o")
                ->leftJoin("o.factura", "f")
                ->leftJoin("o.client", "c")
                ->where('o.date BETWEEN :dateStart AND :dateEnd')
                ->andWhere("o.factura IS NULL OR f.bloqueada = 0")
                ->orderBy("c.numAbonado", "ASC")
                ->addOrderBy("o.date", "ASC")
                ->setParameter('dateStart', $dateStart)
                ->setParameter('dateEnd', $dateEnd);
            $resultItems
                ->andWhere("o.client IN (:clients)")
                ->setParameter('clients', $clients);
            $results = $resultItems
                ->getQuery()
                ->getResult();

            $clients = [];
            foreach ($results as $item) {
                if ($item->client) {
                    if (!isset($clients[$item->client->id]))
                        $clients[$item->client->id] = [
                            "client" => $item->client,
                            "items" => []
                        ];
                    $clients[$item->client->id]["items"][] = $item;
                }
            }
            if (!$clients)
                return new Http\JsonResponse(['message' => "Sin resultados: No hay pendientes de facturar"], 404);

            $zipFile = "facturas-" . time();
            $cacheDirectory = FileSystem::getPath(Directory::APP) . '/cache';

            if (!is_dir($cacheDirectory . "/pdf/" . $zipFile))
                mkdir($cacheDirectory . "/pdf/" . $zipFile, 0777, true);

            $serie = Config::get("modules.factura.factura.serie");
            $dateStart2 = new DateTime();
            $dateStart2->setDate($dateStart2->format('Y'), 1, 1)->setTime(0, 0);
            $dateEnd2 = clone $dateStart2;
            $dateEnd2->add(new DateInterval("P1Y"))->sub(new DateInterval("P1D"));
            foreach ($clients as $c) {
                $dateReference = clone $dateFactura;
                $dateReference->setDate($dateReference->format('Y'), 1, 1)->setTime(0, 0);
                $dateEndReference = clone $dateStart2;
                $dateEndReference->add(new DateInterval("P1Y"))->sub(new DateInterval("P1D"));
                $reference = Factura::getLastReference($serie, $dateReference, $dateEndReference);
                $number = static::parseFormat($serie, $dateReference, $reference);
                $f = $em->getRepository(\App\Entity\Document\Factura\Factura::class)->findOneBy(
                    [
                        "client" => $c['client'],
                        "bloqueada" => false
                    ]
                );
                if (!$f) {
                    $f = new \App\Entity\Document\Factura\Factura();
                    $f->number = $number;
                    $f->serie = $serie;
                    $f->reference = $reference;
                    $f->client = $c['client'];
                    $f->date = $dateFactura;
                    if (empty($f->number))
                        $f->status = DocumentStatus::DRAFT;
                    else
                        $f->status = DocumentStatus::ACTIVE;
                } else {
                    $lineas = $em->getRepository(FacturaLinea::class)->findBy(['factura' => $f]);
                    foreach ($lineas as $l) {
                        $lineasAlbaran = $em->getRepository(AlbaranLinea::class)->findBy(['facturaLinea' => $l]);
                        foreach ($lineasAlbaran as $la) {
                            $la->facturaLinea = null;
                            $em->persist($la);
                        }
                        $em->remove($l);
                    }
                }

                $f->albaran = $c['items'][0];
                $em->persist($f);
                $subtotal = 0;
                $total = 0;

                foreach ($c['items'] as $item) {
                    $fn = "findBy" . ucfirst('albaran');
                    $lines = $em->getRepository(AlbaranLinea::class)->$fn($item);
                    foreach ($lines as $l) {
                        $line = new FacturaLinea();
                        $line->albaran = $item;
                        $line->factura = $f;
                        $line->albaranLinea = $l;
                        $line->reference = $l->reference;
                        $line->description = $l->description;
                        $line->discount = $l->discount;
                        $line->price = $l->price;
                        $line->total = $l->total;
                        $subtotal += $l->total;
                        $line->quantity = $l->quantity;
                        $line->tax = $l->tax;
                        $d = $l->total - ($l->total * $c['client']->descuento / 100);
                        $total += ($d + (($d * $l->tax) / 100));
                        $em->persist($line);
                        $l->factura = $f;
                        $l->facturaLinea = $line;
                        $em->persist($l);
                    }
                }
                $f->importeBruto = $subtotal;
                if ($c['client']->descuento) {
                    $f->discount = $c['client']->descuento;
                    $descuento = $subtotal * $c['client']->descuento / 100;
                    $subtotal = $subtotal - $descuento;
                    $f->impDiscount = $descuento;
                }

                $f->subtotal = $subtotal;
                $f->total = $total;
                $f->bloqueada = true;
                $em->persist($f);
                try {
                    foreach ($c['items'] as $item) {
                        $item->factura = $f;
                        $em->persist($item);
                    }
                    $em->flush();
                } catch (Exception $e) {
                    return new Http\JsonResponse(['message' => $e->getMessage()], 500);
                }
                $invoice = Factura::print($f->id, true);
                $invoice->render($cacheDirectory . "/pdf/" . $zipFile . "/" . $f->number . ".pdf", 'F');
            }
            $zip = new ZipArchive;
            if ($zip->open($cacheDirectory . "/pdf/" . $zipFile . "/albaranes.zip", ZipArchive::CREATE)) {
                foreach (glob($cacheDirectory . "/pdf/" . $zipFile . "/*.pdf") as $pdf) {
                    $zip->addFile($pdf, basename($pdf));
                }
                $zip->close();
            } else
                echo 'Failed!';

            header('Content-disposition: attachment; filename=' . \App\Entity\Document\Albaran\Albaran::class . "-" . time() . '.zip');
            header('Content-type: application/zip');
            readfile($cacheDirectory . "/pdf/" . $zipFile . "/albaranes.zip");
            die("fin");
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function export()
    {
        $cacheDirectory = FileSystem::getPath(Directory::APP) . '/cache';
        try {
            $model = MultiClientIntervalDates::fromRequest();

            $dateStart = $model->start instanceof DateTime ? $model->start : new DateTime($model->start);
            $dateEnd = $model->end instanceof DateTime ? $model->end : new DateTime($model->end);

            $clients = [];
            foreach ($model->clients as $client) {
                $clients[] = \App\Entity\Client::findOneBy(['id' => $client->id, 'deletedOn' => null]);
            }

            $em = DB::getManager();
            if (file_exists(new ReflectionClass(FacturaPdf::class)->getFileName()))
                require_once new ReflectionClass(FacturaPdf::class)->getFileName();
            else
                require_once new ReflectionClass(DefaultPdf::class)->getFileName();

            $resultItems = $em
                ->createQueryBuilder()
                ->select('o')
                ->from(\App\Entity\Document\Albaran\Albaran::class, "o")
                ->where('o.date BETWEEN :dateStart AND :dateEnd')
                ->orderBy("o.reference", "DESC")
                ->setParameter('dateStart', $dateStart)
                ->setParameter('dateEnd', $dateEnd);

            if (!empty($clients)) {
                $resultItems
                    ->andWhere('o.client IN (:clients)')
                    ->setParameter('clients', $clients);
            }

            $results = $resultItems
                ->getQuery()
                ->getResult();

            $clients = [];
            foreach ($results as $item) {
                if ($item->client) {
                    if (!isset($clients[$item->client->id]))
                        $clients[$item->client->id] = [
                            "client" => $item->client,
                            "items" => []
                        ];
                    $clients[$item->client->id]["items"][] = $item;
                }
            }
            if (!$clients)
                return new Http\JsonResponse(['message' => "Ningún documento encontrado"], 404);

            $zipFile = "Albaranes-" . time();
            if (!is_dir($cacheDirectory . "/pdf/" . $zipFile))
                mkdir($cacheDirectory . "/pdf/" . $zipFile, 0777, true);

            foreach ($clients as $c) {
                foreach ($c['items'] as $item) {
                    $invoice = self::print($item->id, true);
                    $invoice->render($cacheDirectory . "/pdf/" . $zipFile . "/" . $item->number . ".pdf", 'F');
                }
            }

            $zip = new ZipArchive;
            if ($zip->open($cacheDirectory . "/pdf/" . $zipFile . "/albaranes.zip", ZipArchive::CREATE)) {
                foreach (glob($cacheDirectory . "/pdf/" . $zipFile . "/*.pdf") as $pdf) {
                    $zip->addFile($pdf, basename($pdf));
                }
                $zip->close();
            } else
                echo 'Failed!';

            header('Content-disposition: attachment; filename=' . \App\Entity\Document\Albaran\Albaran::class . "-" . time() . '.zip');
            header('Content-type: application/zip');
            readfile($cacheDirectory . "/pdf/" . $zipFile . "/albaranes.zip");
            die("fin");
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function print(int $id, bool $export = false)
    {
        try {
            if (!$id)
                return new Http\JsonResponse(['message' => "ID no encontrada"], 404);

            $em = DB::getManager();
            $item = $em->getRepository(\App\Entity\Document\Albaran\Albaran::class)->findOneBy(['id' => $id]);
            $lineas = $em->getRepository(AlbaranLinea::class)->findBy(['albaran' => $item]);
            $client = $item->client ?? null;

            if (!$export) {
                if (file_exists(new ReflectionClass(FacturaPdf::class)->getFileName()))
                    require_once new ReflectionClass(FacturaPdf::class)->getFileName();
                else
                    require_once new ReflectionClass(DefaultPdf::class)->getFileName();
            }

            /* Header settings */
            $invoice = new FacturaPdf("A4", "€", "es");
            $logoPath = FileSystem::getPath(Directory::PUBLIC) . '/img/logo/logo-mollet.jpg';
            $invoice->setLogo($logoPath);  //logo image path
            $invoice->setColor("#007fff");      // pdf color scheme
            $invoice->setType(strtoupper("albaran"));   // Invoice Type
            $invoice->reference = $item->number;   // Reference
            $invoice->date = $item->date->format("d-m-Y");   //Billing Date
            $invoice->setNumberFormat(",", ".", "right");

            $invoice->from = [
                Config::get("modules.factura.empresa.nombre_fiscal"),
                Config::get("modules.factura.empresa.nombre_fiscal2"),
                Config::get("modules.factura.empresa.direccion"),
                Config::get("modules.factura.empresa.localidad"),
                Config::get("modules.factura.empresa.cp") . " " . Config::get("modules.factura.empresa.provincia"),
                Config::get("modules.factura.empresa.nif")
            ];

            // Sé que es una guarrada Pedro, pero añadimos 2 líneas en blanco para poder ocultar el nif del cliente de la ventanita de las cartas
            if ($client) {
                $invoice->pedido = $client->numPedido ?: '-';
                $invoice->ibanCliente = $client->iban;
                $invoice->abonado = $client->numAbonado;
                $invoice->nif = $client->nif;
                $invoice->to = [
                    $client->name,
                    $client->direccion,
                    $client->localidad,
                    $client->codigoPostal . " " . $client->provincia,
                    " ",
                    $client->nif
                ];
            }

            foreach ($lineas as $linea) {
                $albaran = $linea->albaran ? $linea->albaran->number : '';
                $date = $linea->albaran ? $linea->albaran->date->format("d/m/Y") : '';
                $invoice->addItem($linea->reference, $linea->description, $linea->quantity, false, $linea->price, $linea->discount, $linea->total, $albaran, $date);
            }

            $invoice->addTotal("Importe Bruto", $item->importeBruto);
            $invoice->addTotal("Dto. Esp " . $item->discount . "%", $item->impDiscount);
            $invoice->addTotal("Dto. P.P. " . $item->discountPP . "%", $item->impDiscountPP);
            $invoice->addTotal("Base Imponible", $item->subtotal);
            $invoice->addTotal("Tipo IVA 21%", $item->total - $item->subtotal);
            $invoice->addTotal("Total", $item->total);

            if ($client)
                $invoice->addParagraph("Forma de pago: " . $client->formaPago);

            $invoice->addParagraph("Vencimiento: " . self::getDueDate($item)->format("d-m-Y"));

            if ($client && $client->formaPago == "TRANSFERENCIA BANCARIA")
                $invoice->addParagraph("IBAN Mollet Express: " . Config::get("modules.factura.empresa.iban"));

            $invoice->footerNote = Config::get("modules.factura.empresa.registro");

            if (!$export) {
                $invoice->render('example1.pdf', 'I');
                exit();
            } else
                return $invoice;

        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function getDueDate(\App\Entity\Document\Albaran\Albaran $item): DateTime|null
    {
        try {
            //Obtenemos el día de cobro de la factura
            $newFecha = clone $item->date;
            //Le sumamos los dias de pago al día de cobro de la factura
            $newFecha->add(new DateInterval("P" . $item->client->diasPago . "D"));

            //Si la fecha nueva es superior al día fijo de pago estipulado
            //Entonces le sumamos un mes a esta fecha
            if ($newFecha->format("j") > $item->client->diaFijoPago)
                $newFecha->add(new DateInterval("P1M"));

            //si el mes tiene menos días que el día de pago fijado del cliente ponemos el último día del mes.
            if ($item->client->diaFijoPago > date("t"))
                $date = $newFecha->modify("last day of this month");
            else {
                $dateFactura = $newFecha->format("Y-m");
                $date = DateTime::createFromFormat("Y-m-d", $dateFactura . "-" . $item->client->diaFijoPago);
            }
            // FIN CALCULO FECHA VENCIMIENTO
            return $date;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function list(): Http\JsonResponse
    {
        try {
            $model = MultiClientIntervalDates::fromRequest();

            $dateStart = $model->start instanceof DateTime ? $model->start : new DateTime($model->start);
            $dateEnd = $model->end instanceof DateTime ? $model->end : new DateTime($model->end);

            $clients = [];
            foreach ($model->clients as $client) {
                $clients[] = \App\Entity\Client::findOneBy(['id' => $client->id, 'deletedOn' => null]);
            }

            $em = DB::getManager();
            if (file_exists(new ReflectionClass(FacturaPdf::class)->getFileName()))
                require_once new ReflectionClass(FacturaPdf::class)->getFileName();
            else
                require_once new ReflectionClass(DefaultPdf::class)->getFileName();

            $resultItems = $em
                ->createQueryBuilder()
                ->select('o')
                ->from(\App\Entity\Document\Albaran\Albaran::class, "o")
                ->innerJoin('o.client', 'c')
                ->where('o.date BETWEEN :dateStart AND :dateEnd')
                ->orderBy("c.numAbonado", "ASC")
                ->setParameter('dateStart', $dateStart)
                ->setParameter('dateEnd', $dateEnd);

            if (!empty($clients)) {
                $resultItems
                    ->andWhere('o.client IN (:clients)')
                    ->setParameter('clients', $clients);
            }

            $result = $resultItems
                ->getQuery()
                ->getResult();

            $data = [];
            $subtotal = 0;
            $iva = 0;
            $total = 0;
            foreach ($result as $item) {
                $data[] = [
                    $item->number,
                    $item->date->format('d/m/Y'),
                    str_pad($item->client->numAbonado, 4, "0", STR_PAD_LEFT),
                    mb_convert_encoding(substr($item->client->name, 0, 50), 'ISO-8859-1', 'UTF-8'),
                    $item->client->nif,
                    $item->subtotal,
                    ($item->total - $item->subtotal),
                    $item->total
                ];
                $subtotal += $item->subtotal;
                $iva += ($item->total - $item->subtotal);
                $total += $item->total;
            }
            $data[] = [
                '',
                '',
                '',
                '',
                'Suma:',
                $subtotal,
                $iva,
                $total
            ];

            require_once new ReflectionClass(ListadoPdf::class)->getFileName();

            $pdf = new ListadoPdf();
            $pdf->AliasNbPages();
            // Column headings
            $header = ['Numero', 'Fecha', 'Código', 'Nombre', 'NIF', 'Bruto', 'IVA', 'TOTAL'];
            $header = array_map(function ($item) {
                return mb_convert_encoding($item, 'ISO-8859-1', 'UTF-8');
            }, $header);

            $pdf->AddPage();
            $pdf->resetFont();
            $pdf->title('Listado de ' . (new ReflectionClass(\App\Entity\Document\Albaran\Albaran::class)->getShortName()));
            $pdf->ImprovedTable($header, $data);
            $pdf->Output();
            die();
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function sendEmail(int $id): Http\JsonResponse
    {
        try {
            $cacheDirectory = FileSystem::getPath(Directory::APP) . '/cache';
            if (!$id)
                return new Http\JsonResponse(['message' => "ID no encontrada"], 404);

            $em = DB::getManager();
            $files = [];
            if (file_exists(new ReflectionClass(FacturaPdf::class)->getFileName()))
                require_once new ReflectionClass(FacturaPdf::class)->getFileName();
            else
                require_once new ReflectionClass(DefaultPdf::class)->getFileName();

            if (!is_dir($cacheDirectory . '/pdf'))
                mkdir($cacheDirectory . '/pdf', 0777, true);

            $item = $em->getRepository(\App\Entity\Document\Albaran\Albaran::class)->findOneBy(['id' => $id]);
            $invoice = self::print($item->id, true);
            $invoice->render($cacheDirectory . "/pdf/" . hash("sha256", $item->id) . ".pdf", 'F');
            if (!$item->client)
                return new Http\JsonResponse(['message' => "El documento seleccionado no tiene cliente asignado"], 400);

            $files[$item->client->id][] = [
                "name" => $item->number . ".pdf",
                "file" => $cacheDirectory . "/pdf/" . hash("sha256", $item->id) . ".pdf"
            ];

            $mail = false;
            if ($item != null) {
                foreach ($files as $list) {
                    $addresses = [];
                    foreach (explode(",", str_replace(" ", "", $item->client->email)) as $key => $address) {
                        $addresses[] = [
                            "type" => $key ? "cc" : "to",
                            "email" => $address,
                            "name" => $item->client->email,
                        ];
                    }
                    $sent = Mail::send(
                        $addresses,
                        "Nuevo documento de " . (new ReflectionClass(\App\Entity\Document\Albaran\Albaran::class)->getShortName()),
                        '<html lang="">
                        <head>
                            <meta charset="utf8">
                        </head>
                        <body>

                        </body>
                    </html>',
                        $list
                    );
                    if ($sent)
                        $mail = true;
                }
            }

            if ($mail) {
                $item->emailSent = true;
                $em->persist($item);
                $em->flush();
                return new Http\JsonResponse(['message' => "Documento enviado correctamente"]);
            } else
                return new Http\JsonResponse(['message' => "Falló el envío del documento", 500]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    private static function parseFormat(string $serie = "", ?object $date = NULL, int $reference = 0, string $class = ''): string
    {
        $class = $class ? $class : 'factura';

        $format = Config::get("modules.factura." . $class . ".formato");
        if (!$format) {
            if (isset(self::$config[$class]['formato']))
                $format = self::$config[$class]['formato'];
        }

        $length = Config::get("modules.factura." . $class . ".ref_length");
        if (!$length) {
            if (isset(self::$config[$class]['ref_length']))
                $format = self::$config[$class]['ref_length'];
        }

        $format = str_replace("{serie}", $serie, $format);
        $format = str_replace("{year}", $date->format("y"), $format);
        return str_replace("{reference}", str_pad($reference, $length, "0", STR_PAD_LEFT), $format);
    }

    public static function generateReference(int $id = 0, string $serie = 'A'): Http\JsonResponse
    {
        try {

            if ($id !== 0) {
                $item = \App\Entity\Document\Albaran\Albaran::findOneBy(['id' => $id, 'deletedOn' => null]);

                if ($item->number)
                    return new Http\JsonResponse(['reference' => $item->reference, 'number' => $item->number]);
                else
                    $serie = $item->serie;
            }

            $date_start = new DateTime();
            $date_start->setDate($date_start->format('Y'), 1, 1)->setTime(0, 0);
            $date_end = clone $date_start;
            $date_end->add(new DateInterval("P1Y"))->sub(new DateInterval("P1D"));
            $query_res = DB::getManager()->getRepository(\App\Entity\Document\Albaran\Albaran::class)
                ->createQueryBuilder('p')
                ->where('p.reference > 0 AND p.date BETWEEN :date_start AND :date_end AND p.serie = :serie')
                ->orderBy("p.reference", "DESC")
                ->setParameter('serie', $serie)
                ->setParameter('date_start', $date_start)
                ->setParameter('date_end', $date_end)
                ->getQuery()
                ->setMaxResults(1)
                ->getOneOrNullResult();

            if ($query_res) {
                $reference = $query_res->reference + 1;
                if (!$reference) {
                    $reference = 1;
                }
            } else {
                $reference = 1;
            }

            $number = $serie . sprintf('2%02d-%03d', $date_start->format('Y') % 100, $reference);

            return new Http\JsonResponse(['reference' => $reference, 'number' => $number]);
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }
}