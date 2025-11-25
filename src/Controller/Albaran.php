<?php

namespace App\Controller;

use App\Model\DataTable;
use Cavesman\Db;
use Cavesman\Enum\Directory;
use Cavesman\Exception\ModuleException;
use Cavesman\FileSystem;
use Cavesman\Http;
use Cavesman\Mail;
use Cavesman\Request;
use Cavesman\Twig;
use DateTime;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Albaran
{
    public static function filterAll(): Http\JsonResponse
    {
        try {
            $em = Db::getManager();

            $qb = $em->getRepository(\App\Entity\Document\Albaran\Albaran::class)
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

            /** @var \App\Entity\Document\Albaran\Albaran[] $list */
            $list = $qb->getQuery()->getResult();

            $datatable = new DataTable();
            $datatable->recordsTotal = count($total->getQuery()->getResult());
            foreach ($list as $item) {
                /** @var \App\Model\Document\Albaran\Albaran $model */
                $model = $item->model(\App\Model\Document\Albaran\Albaran::class);
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

            $qb = $em->getRepository(\App\Entity\Document\Albaran\Albaran::class)
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

            /** @var \App\Entity\Document\Albaran\Albaran[] $list */
            $list = $qb->getQuery()->getResult();

            $datatable = new DataTable();
            $datatable->recordsTotal = count($total->getQuery()->getResult());
            foreach ($list as $item) {
                /** @var \App\Model\Document\Albaran\Albaran $model */
                $model = $item->model(\App\Model\Document\Albaran\Albaran::class);
                $datatable->data[] = $model->json();
            }
            $datatable->recordsFiltered = count($total->getQuery()->getResult());

            return new  Http\JsonResponse($datatable);
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }

    }

    /**
     * @param \App\Entity\Document\Albaran\Albaran $item
     * @return void
     * @throws LoaderError
     * @throws ModuleException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function notifyCreated(\App\Entity\Document\Albaran\Albaran $item): void
    {
        // Buscar receptores de la notificación
        $employees = self::getNotificationReceivers(\App\Enum\Role::RECEIVE_EMAIL_CREATE->value);

        // Preparar mensaje y enviar
        foreach ($employees as $employee) {
            $body = Twig::render('mail/partial/new-delivery-note.html.twig', ['item' => $item, 'title' => 'Nuevo albarán']);
            $text = Twig::renderFromString($body);
            $mail = Mail::getInstance();
            $mail->addEmbeddedImage(FileSystem::getPath(Directory::PUBLIC) . '/img/logo/logo-blue.png', 'logo');
            Mail::send($employee->email, 'Nuevo albarán en MolletExpress', ['html' => $body, 'text' => $text]);
        }

    }

    /**
     * @param \App\Entity\Document\Albaran\Albaran $item
     * @return void
     * @throws LoaderError
     * @throws ModuleException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function notifyEdited(\App\Entity\Document\Albaran\Albaran $item): void
    {
        // Buscar receptores de la notificación
        $employees = self::getNotificationReceivers(\App\Enum\Role::RECEIVE_EMAIL_EDIT->value);

        // Preparar mensaje y enviar
        foreach ($employees as $employee) {
            $body = Twig::render('mail/partial/new-delivery-note.html.twig', ['item' => $item, 'title' => 'Albarán actualizado']);
            $text = Twig::renderFromString($body);


            $mail = Mail::getInstance();

            $mail->addEmbeddedImage(FileSystem::getPath(Directory::PUBLIC) . '/img/logo/logo-blue.png', 'logo');
            Mail::send($employee->email, 'Albarán actualizado en MolletExpress', ['html' => $body, 'text' => $text]);
        }
    }

    /**
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws ModuleException
     */
    public static function notifyDeleted(\App\Entity\Document\Albaran\Albaran $item): void
    {
        // Buscar receptores de la notificación
        $employees = self::getNotificationReceivers(\App\Enum\Role::RECEIVE_EMAIL_DELETE->value);

        // Preparar mensaje y enviar
        foreach ($employees as $employee) {
            $body = Twig::render('mail/partial/new-delivery-note.html.twig', ['item' => $item, 'title' => 'Albarán eliminado']);
            $text = Twig::renderFromString($body);
            $mail = Mail::getInstance();
            $mail->addEmbeddedImage(FileSystem::getPath(Directory::PUBLIC) . '/img/logo/logo-blue.png', 'logo');
            Mail::send($employee->email, 'Albarán eliminado en MolletExpress', ['html' => $body, 'text' => $text]);
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

            if (!$model->client || !$model->ref)
                return new Http\JsonResponse(['message' => 'No se ha recibido todos los datos requeridos *'], 400);

            if (is_string($model->employee->logo))
                $model->employee->logo = \App\Enum\Images::from($model->employee->logo);
            if (is_string($model->employee->icono))
                $model->employee->icono = \App\Enum\Images::from($model->employee->icono);
            if (is_string($model->employee->fondo))
                $model->employee->fondo = \App\Enum\Images::from($model->employee->fondo);

            if ($model->employee->comercial) {
                if (is_string($model->employee->comercial->logo))
                    $model->employee->comercial->logo = \App\Enum\Images::from($model->employee->comercial->logo);
                if (is_string($model->employee->comercial->icono))
                    $model->employee->comercial->icono = \App\Enum\Images::from($model->employee->comercial->icono);
                if (is_string($model->employee->comercial->fondo))
                    $model->employee->comercial->fondo = \App\Enum\Images::from($model->employee->comercial->fondo);
            }

            /** @var \App\Entity\Document\Albaran\Albaran $entity */
            $entity = $model->entity();

            $em = DB::getManager();

            $em->persist($entity);
            $em->flush();


            try {
                self::notifyCreated($entity);
            } catch (Exception $e) {

            }

            return new Http\JsonResponse([
                'message' => "Albarán añadido correctamente",
                'item' => $entity->model(\App\Model\Document\Albaran\Albaran::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function update(int $id): Http\JsonResponse
    {
        set_time_limit(5);
        ini_set('max_execution_time', '5');


        try {
            $item = \App\Entity\Document\Albaran\Albaran::findOneBy(['id' => $id, 'deletedOn' => null]);

            if (!$item)
                return new Http\JsonResponse(['message' => "Albarán no encontrado"], 404);

            $model = \App\Model\Document\Albaran\Albaran::fromRequest();

            if (!$model->client || !$model->ref)
                return new Http\JsonResponse(['message' => 'No se ha recibido todos los datos requeridos *'], 400);

            if ($id != $model->id)
                return new Http\JsonResponse(['message' => "La id indicada en la url no corresponde a la enviada en el modelo"], 404);

            if (is_string($model->employee->logo))
                $model->employee->logo = \App\Enum\Images::from($model->employee->logo);
            if (is_string($model->employee->icono))
                $model->employee->icono = \App\Enum\Images::from($model->employee->icono);
            if (is_string($model->employee->fondo))
                $model->employee->fondo = \App\Enum\Images::from($model->employee->fondo);

            if ($model->employee->comercial) {
                if (is_string($model->employee->comercial->logo))
                    $model->employee->comercial->logo = \App\Enum\Images::from($model->employee->comercial->logo);
                if (is_string($model->employee->comercial->icono))
                    $model->employee->comercial->icono = \App\Enum\Images::from($model->employee->comercial->icono);
                if (is_string($model->employee->comercial->fondo))
                    $model->employee->comercial->fondo = \App\Enum\Images::from($model->employee->comercial->fondo);
            }

            /** @var \App\Entity\Document\Albaran\Albaran $entity */
            $entity = $model->entity();
            $em = DB::getManager();
            $em->persist($entity);
            $em->flush();
            try {
                self::notifyEdited($entity);
            } catch (Exception $e) {

            }

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

            $item->deletedOn = new DateTime();

            $em->persist($item);
            $em->flush();

            try {
                self::notifyDeleted($item);
            } catch (Exception $e) {

            }

            return new Http\JsonResponse([
                'message' => "Albarán eliminado correctamente",
                'item' => $item->model(\App\Model\Document\Albaran\Albaran::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @param string $roleReceive
     * @return array
     * @throws ModuleException
     */
    public static function getNotificationReceivers(string $roleReceive): array
    {
        $employees = [];
        foreach (\App\Entity\Employee\Employee::findBy(['deletedOn' => null]) as $employee) {
            $canReceiver = false;
            // Comprobar si puede recibir notificación de albaranes creados/actualizados/borrados
            // y si le corresponde el albarán (Si puede verlo)
            foreach ($employee->roles as $role) {
                if ($role->role->value == $roleReceive && $role->group->value === 'DELIVERY_NOTE' && $role->active)
                    $canReceiver = true;
            }
            if ($canReceiver) {
                $canReceiver = false;
                foreach ($employee->roles as $role) {
                    if ($role->role->value == 'VIEW_ALL' && $role->group->value === 'DELIVERY_NOTE' && $role->active)
                        $canReceiver = true;
                }
                if ($canReceiver)
                    $employees[] = $employee;
            }
        }
        return $employees;
    }
}