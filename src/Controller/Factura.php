<?php

namespace App\Controller;

use App\Enum\DocumentStatus;
use App\Model\DataTable;
use Cavesman\Db;
use Cavesman\Enum\Directory;
use Cavesman\FileSystem;
use Cavesman\Http;
use Cavesman\Request;
use DateTime;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use ReflectionClass;

class Factura
{
    public static function filterAll(): Http\JsonResponse
    {
        try {
            $em = Db::getManager();

            $qb = $em->getRepository(\App\Entity\Document\Factura\Factura::class)
                ->createQueryBuilder('i')
                ->where('i.deletedOn IS NULL');

            $filter = json_decode(Request::get('filter', '[]'));

            if ($filter && $filter->search) {
                foreach (explode(' ', $filter->search->value) as $key => $string) {
                    $qb
                        ->andWhere('i.serie LIKE :search_' . $key . ' OR i.number LIKE :search_' . $key
                            . ' OR i.observaciones LIKE :search_' . $key . ' OR i.tax LIKE :search_' . $key
                            . ' OR i.comments LIKE :search_' . $key
                        )
                        ->setParameter('search_' . $key, '%' . $string . '%');
                }
            }

            if ($filter->order && $filter->columns) {
                foreach ($filter->order as $order) {
                    $index = $order->column;
                    $columnName = $filter->columns[$index]->data;
                    $dir = strtoupper($order->dir);
                    if ($dir === 'ASC' || $dir === 'DESC')
                        $qb->addOrderBy('i.' . $columnName, $dir);
                }
            }

            $total = clone $qb;

            if ($filter->length ?? false) {
                $qb->setMaxResults($filter->length)
                    ->setFirstResult($filter->start);
            }

            /** @var \App\Entity\Document\Factura\Factura[] $list */
            $list = $qb->getQuery()->getResult();

            $datatable = new DataTable();
            $datatable->recordsTotal = count($total->getQuery()->getResult());
            foreach ($list as $item) {
                /** @var \App\Model\Document\Factura\Factura $model */
                $model = $item->model(\App\Model\Document\Factura\Factura::class);
                $datatable->data[] = $model->json();
            }
            $datatable->recordsFiltered = count($total->getQuery()->getResult());

            return new  Http\JsonResponse($datatable);
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }

    }

    public static function filter(int $id): Http\JsonResponse
    {
        try {
            $em = Db::getManager();

            $qb = $em->getRepository(\App\Entity\Document\Factura\Factura::class)
                ->createQueryBuilder('i')
                ->where('i.deletedOn IS NULL')
                ->andWhere('c.id = :id')
                ->setParameter('id', $id);


            $filter = json_decode(Request::get('filter', '[]'));

            if ($filter && $filter->search) {
                foreach (explode(' ', $filter->search->value) as $key => $string) {
                    $qb
                        ->andWhere('i.serie LIKE :search_' . $key . ' OR i.number LIKE :search_' . $key
                            . ' OR i.observaciones LIKE :search_' . $key . ' OR i.tax LIKE :search_' . $key
                            . ' OR i.comments LIKE :search_' . $key
                        )
                        ->setParameter('search_' . $key, '%' . $string . '%');
                }
            }

            if ($filter->order && $filter->columns) {
                foreach ($filter->order as $order) {
                    $index = $order->column;
                    $columnName = $filter->columns[$index]->data;
                    $dir = strtoupper($order->dir);
                    if ($dir === 'ASC' || $dir === 'DESC')
                        $qb->addOrderBy('i.' . $columnName, $dir);
                }
            }

            $total = clone $qb;

            if ($filter->length ?? false) {
                $qb->setMaxResults($filter->length)
                    ->setFirstResult($filter->start);
            }

            /** @var \App\Entity\Document\Factura\Factura[] $list */
            $list = $qb->getQuery()->getResult();

            $datatable = new DataTable();
            $datatable->recordsTotal = count($total->getQuery()->getResult());
            foreach ($list as $item) {
                /** @var \App\Model\Document\Factura\Factura $model */
                $model = $item->model(\App\Model\Document\Factura\Factura::class);
                $datatable->data[] = $model->json();
            }
            $datatable->recordsFiltered = count($total->getQuery()->getResult());

            return new  Http\JsonResponse($datatable);
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }

    }

    public static function get(int $id): Http\JsonResponse
    {
        try {

            $item = \App\Entity\Document\Factura\Factura::findOneBy(['id' => $id, 'deletedOn' => null]);

            return new Http\JsonResponse($item->model(\App\Model\Document\Factura\Factura::class)->json());
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function add(): Http\JsonResponse
    {
        try {

            $model = \App\Model\Document\Factura\Factura::fromRequest();

            /** @var \App\Entity\Document\Factura\Factura $entity */
            $entity = $model->entity();

            $em = DB::getManager();

            foreach ($entity->lineas as $linea) {
                $linea->factura = $entity;
            }

            $em->persist($entity);
            $em->flush();
            $em->persist($entity);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Factura añadida correctamente",
                'item' => $entity->model(\App\Model\Document\Factura\Factura::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function update(int $id): Http\JsonResponse
    {

        try {
            $item = \App\Entity\Document\Factura\Factura::findOneBy(['id' => $id, 'deletedOn' => null]);

            if (!$item)
                return new Http\JsonResponse(['message' => "Factura no encontrada"], 404);

            if ($item->status !== DocumentStatus::DRAFT)
                return new Http\JsonResponse(['message' => "No es posible modificar esta factura ya que ya ha sido enviada"], 404);

            $model = \App\Model\Document\Factura\Factura::fromRequest();

            if ($id != $model->id)
                return new Http\JsonResponse(['message' => "La id indicada en la url no corresponde a la enviada en el modelo"], 404);

            $em = DB::getManager();

            $idLineas = array_map(fn($linea) => $linea->id, $model->lineas);

            foreach ($item->lineas as $linea) {
                if (!in_array($linea->id, $idLineas)) {
                    $linea->deletedOn = new DateTime();
                    $em->persist($linea);
                }
            }

            /** @var \App\Entity\Document\Factura\Factura $entity */
            $entity = $model->entity();

            foreach ($entity->lineas as $linea) {
                $linea->factura = $entity;
            }

            $em->persist($entity);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Factura actualizada correctamente",
                'item' => $entity->model(\App\Model\Document\Factura\Factura::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function delete(int $id): Http\JsonResponse
    {
        try {

            $em = DB::getManager();

            $item = \App\Entity\Document\Factura\Factura::findOneBy(['id' => $id, 'deletedOn' => null]);

            $item->deletedOn = new DateTime();

            $em->persist($item);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Factura eliminada correctamente",
                'item' => $item->model(\App\Model\Document\Factura\Factura::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }


    protected static function getClass(): array|false|string|null
    {
        return mb_strtolower(new ReflectionClass(static::class)->getShortName());
    }

    protected static function getEntity($entity = ''): string
    {
        return "\\src\\Modules\\Factura\\Entity\\" . ucfirst(self::getClass()) . ucfirst($entity) . "Entity";
    }

    protected static function export(string $dateStart, string $dateEnd, string $client)
    {
        $cacheDirectory = FileSystem::getPath(Directory::APP) . '/cache';
        try {
            $em = DB::getManager();
            if (file_exists(dirname(__FILE__) . "/templates/" . self::getClass() . ".php"))
                require dirname(__FILE__) . "/templates/" . self::getClass() . ".php";
            else
                require dirname(__FILE__) . "/templates/default.php";
            // Género los namespace de los documentos a generar
            $namespace = self::getEntity();

            $result_items = $em
                ->createQueryBuilder()
                ->select('o')
                ->from($namespace, "o")
                ->where('o.date BETWEEN :dateStart AND :dateEnd')
                ->orderBy("o.reference", "DESC")
                ->setParameter('dateStart', new DateTime($dateStart))
                ->setParameter('dateEnd', new DateTime($dateEnd));
            if ($client !== "false") {
                $client_ent = $em->getReference(\App\Entity\Client::class, $client);
                $result_items
                    ->andWhere("o.client = :client")
                    ->setParameter('client', $client_ent);
            }
            $results = $result_items
                ->getQuery()
                ->getResult();


            $clients = [];
            foreach ($results as $item) {
                if ($item->getClient()) {
                    if (!isset($clients[$item->getClient()->getId()]))
                        $clients[$item->getClient()->getId()] = [
                            "client" => $item->getClient(),
                            "items" => []
                        ];
                    $clients[$item->getClient()->getId()]["items"][] = $item;
                }
            }
            if (!$clients) {
                return new Http\JsonResponse(['message' => "Ningún documento encontrado"], 404);
            }

            $zip_file = self::getClass() . "-" . time();
            if (!is_dir($cacheDirectory . "/pdf/" . $zip_file))
                mkdir($cacheDirectory . "/pdf/" . $zip_file, 0777, true);

            foreach ($clients as $c) {
                foreach ($c['items'] as $item) {
                    $invoice = new FacturaPDF("A4", "€", "es");
                    $invoice = self::print($item->getId(), true, $invoice);
                    $invoice->render($cacheDirectory . "/pdf/" . $zip_file . "/" . $item->getNumber() . ".pdf", 'F');
                }
            }

            $zip = new \ZipArchive;
            if ($zip->open($cacheDirectory . "/pdf/" . $zip_file . "/facturas.zip", \ZipArchive::CREATE)) {
                foreach (glob($cacheDirectory . "/pdf/" . $zip_file . "/*.pdf") as $pdf) {
                    $zip->addFile($pdf, basename($pdf));
                }
                $zip->close();
            } else {
                echo 'Failed!';
            }
            header('Content-disposition: attachment; filename=' . self::getClass() . "-" . time() . '.zip');
            header('Content-type: application/zip');
            readfile($cacheDirectory . "/pdf/" . $zip_file . "/facturas.zip");
            die("fin");
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

}