<?php
namespace App\Controller;

use App\Model\DataTable;
use Cavesman\Db;
use Cavesman\Http;
use Cavesman\Request;
use DateTime;
use Doctrine\ORM\Exception\ORMException;
use Exception;

class OrdainCharge
{

    public static function list(): Http\JsonResponse
    {
        try {

            $list = \App\Entity\OrdainCharge::findBy(['deletedOn' => null]);

            return new Http\JsonResponse(array_map(fn(\App\Entity\OrdainCharge $ordainCharge) => $ordainCharge->model(\App\Model\OrdainCharge::class)->json(), $list));
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function filterAll(): Http\JsonResponse
    {
        try {
            $em = Db::getManager();

            $qb = $em->getRepository(\App\Entity\OrdainCharge::class)
                ->createQueryBuilder('i')
                ->where('i.deletedOn IS NULL');

            $filter = json_decode(Request::get('filter', '[]'));

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

            /** @var \App\Entity\OrdainCharge[] $list */
            $list = $qb->getQuery()->getResult();

            $datatable = new DataTable();
            $datatable->recordsTotal = count($total->getQuery()->getResult());
            foreach ($list as $item) {
                /** @var \App\Model\OrdainCharge $model */
                $model = $item->model(\App\Model\OrdainCharge::class);
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

            $qb = $em->getRepository(\App\Entity\OrdainCharge::class)
                ->createQueryBuilder('i')
                ->where('i.deletedOn IS NULL');

            $filter = json_decode(Request::get('filter', '[]'));

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

            /** @var \App\Entity\OrdainCharge[] $list */
            $list = $qb->getQuery()->getResult();

            $datatable = new DataTable();
            $datatable->recordsTotal = count($total->getQuery()->getResult());
            foreach ($list as $item) {
                /** @var \App\Model\OrdainCharge $model */
                $model = $item->model(\App\Model\OrdainCharge::class);
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

            $item = \App\Entity\OrdainCharge::findOneBy(['id' => $id, 'deletedOn' => null]);

            return new Http\JsonResponse($item->model(\App\Model\OrdainCharge::class)->json());
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function add(): Http\JsonResponse
    {
        try {

            $model = \App\Model\OrdainCharge::fromRequest();

            if (!$model->ref)
                return new Http\JsonResponse(['message' => 'No se ha recibido todos los datos requeridos *'], 400);

            /** @var \App\Entity\OrdainCharge $entity */
            $entity = $model->entity();

            $em = DB::getManager();
            $em->persist($entity);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Orden de cargo añadido correctamente",
                'item' => $entity->model(\App\Model\OrdainCharge::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function update(int $id): Http\JsonResponse
    {
        try {
            $item = \App\Entity\OrdainCharge::findOneBy(['id' => $id, 'deletedOn' => null]);

            if (!$item)
                return new Http\JsonResponse(['message' => "Orden de cargo no encontrado"], 404);

            $model = \App\Model\OrdainCharge::fromRequest();

            if (!$model->ref)
                return new Http\JsonResponse(['message' => 'No se ha recibido todos los datos requeridos *'], 400);

            if ($id != $model->id)
                return new Http\JsonResponse(['message' => "La id indicada en la url no corresponde a la enviada en el modelo"], 404);

            /** @var \App\Entity\OrdainCharge $entity */
            $entity = $model->entity();
            $em = DB::getManager();
            $em->persist($entity);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Orden de cargo actualizado correctamente",
                'item' => $entity->model(\App\Model\OrdainCharge::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function delete(int $id): Http\JsonResponse
    {
        try {

            $em = DB::getManager();

            $item = \App\Entity\OrdainCharge::findOneBy(['id' => $id, 'deletedOn' => null]);

            $item->deletedOn = new DateTime();

            $em->persist($item);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Orden de cargo eliminado correctamente",
                'item' => $item->model(\App\Model\OrdainCharge::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

}