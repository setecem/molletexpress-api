<?php

namespace App\Controller;

use App\Model\DataTable;
use App\Model\Document\Factura\FacturaFeed;
use App\Model\Document\Factura\FacturaFeedAlert;
use Cavesman\Config;
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

class Factura
{

    public static function sendTodayAlerts(): Http\JsonResponse
    {

        try {
            $em = DB::getManager();
            $alerts = $em->createQueryBuilder()
                ->select('a')
                ->from(\App\Entity\Document\Factura\FacturaFeedAlert::class, 'a')
                ->where('a.date BETWEEN :from AND :to')
                ->setParameter('from', new DateTime()->setTime(0, 0))
                ->setParameter('to', new DateTime()->setTime(23, 59, 59))
                ->orderBy('a.date', 'ASC')
                ->getQuery()
                ->getResult();

            if (!$alerts) {
                return new Http\JsonResponse(['Sin alertas']);
            }

            $body = '<h3>Alertas de hoy:</h3>';

            /** @var FacturaFeedAlert $alert */
            foreach ($alerts as $alert) {
                $body .= "<h4>" . $alert->feed->factura->enterprise . "</h4>";
                $body .= "<b>" . $alert->date->format("H:i") . ": " . nl2br($alert->description) . "</b><br>";
                $body .= "Seguimiento del día " . $alert->feed->date->format("d/m/Y H:i") . "<br>";
                $body .= nl2br($alert->feed->description) . "<br><hr>";
            }

            Mail::send([["type" => 'to', "email" => Config::get('params.invoice.alert.email', ''), "name" => Config::get('params.invoice.alert.name', '')]], 'Notificaciones de hoy', $body);
            foreach ($alerts as $alert) {
                $em->remove($alert);
            }
            $em->flush();

            return new Http\JsonResponse(['ALL OK']);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function listOrigen(): Http\JsonResponse
    {
        try {
            $list = \App\Entity\Document\Factura\Factura::findBy(['deletedOn' => null]);
            $origen = [];
            foreach ($list as $item) {
                if (!in_array($item->origen, $origen) && $item->origen != null)
                    $origen[] = $item->origen;
            }
            return new Http\JsonResponse(['origen' => $origen]);
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function filterAll(): Http\JsonResponse
    {
        try {
            $em = Db::getManager();

            $qb = $em->getRepository(\App\Entity\Document\Factura\Factura::class)
                ->createQueryBuilder('i')
                ->join('i.employee', 'c')
                ->where('i.deletedOn IS NULL');

            $filter = json_decode(Request::get('filter', '[]'));

            if ($filter && $filter->search) {
                foreach (explode(' ', $filter->search->value) as $key => $string) {
                    $qb
                        ->andWhere('i.enterprise LIKE :search_' . $key . ' OR i.enterprise_email LIKE :search_' . $key
                            . ' OR i.enterprise_mobile LIKE :search_' . $key . ' OR i.enterprise_phone LIKE :search_' . $key
                            . ' OR i.web LIKE :search_' . $key . ' OR c.name LIKE :search_' . $key . ' OR c.lastname LIKE :search_' . $key
                            . ' OR i.type LIKE :search_' . $key . ' OR i.origen LIKE :search_' . $key
                            . ' OR i.sector LIKE :search_' . $key . ' OR i.sector LIKE :search_' . $key
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

            $employee = \App\Entity\Employee\Employee::findOneBy(['id' => $id, 'deletedOn' => null]);

            $qb = $em->getRepository(\App\Entity\Document\Factura\Factura::class)
                ->createQueryBuilder('i')
                ->join('i.employee', 'c')
                ->where('i.deletedOn IS NULL');
            if ($employee->comercial) {
                $qb
                    ->andWhere('c.id = :id OR c.id = :comercial_id')
                    ->setParameter('id', $id)
                    ->setParameter('comercial_id', $employee->comercial->id);
            } else {
                $qb
                    ->andWhere('c.id = :id')
                    ->setParameter('id', $id);
            }

            $filter = json_decode(Request::get('filter', '[]'));

            if ($filter && $filter->search) {
                foreach (explode(' ', $filter->search->value) as $key => $string) {
                    $qb
                        ->andWhere('i.enterprise LIKE :search_' . $key . ' OR i.enterprise_email LIKE :search_' . $key
                            . ' OR i.enterprise_mobile LIKE :search_' . $key . ' OR i.enterprise_phone LIKE :search_' . $key
                            . ' OR i.web LIKE :search_' . $key . ' OR c.name LIKE :search_' . $key . ' OR c.lastname LIKE :search_' . $key
                            . ' OR i.type LIKE :search_' . $key . ' OR i.origen LIKE :search_' . $key
                            . ' OR i.sector LIKE :search_' . $key . ' OR i.sector LIKE :search_' . $key
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

    public static function filterAllFeed(int $id): Http\JsonResponse
    {
        try {
            $em = Db::getManager();

            $qb = $em->getRepository(\App\Entity\Document\Factura\FacturaFeed::class)
                ->createQueryBuilder('i')
                ->where('i.factura = :id')
                ->andWhere('i.deletedOn IS NULL')
                ->setParameter('id', $id);

            $filter = json_decode(Request::get('filter', '[]'));

            if ($filter && $filter->search) {
                foreach (explode(' ', $filter->search->value) as $key => $string) {
                    $qb = $qb
                        ->andWhere('i.date LIKE :search_' . $key . ' OR i.description LIKE :search_' . $key . ' OR i.type LIKE :search_' . $key)
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
                $qb = $qb->setMaxResults($filter->length)
                    ->setFirstResult($filter->start);
            }

            /** @var \App\Entity\Document\Factura\FacturaFeed[] $list */
            $list = $qb->getQuery()->getResult();

            $datatable = new DataTable();
            $datatable->recordsTotal = count($total->getQuery()->getResult());
            foreach ($list as $item) {
                $datatable->data[] = $item->model(FacturaFeed::class)->json();
            }
            $datatable->recordsFiltered = count($total->getQuery()->getResult());

            return new  Http\JsonResponse($datatable);

        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function filterPublicFeed(int $id): Http\JsonResponse
    {
        try {
            $em = Db::getManager();

            $qb = $em->getRepository(\App\Entity\Document\Factura\FacturaFeed::class)
                ->createQueryBuilder('i')
                ->where('i.factura = :id')
                ->andWhere('i.deletedOn IS NULL')
                ->andWhere('i.private = :private')
                ->setParameter('id', $id)
                ->setParameter('private', '0');

            $filter = json_decode(Request::get('filter', '[]'));

            if ($filter && $filter->search) {
                foreach (explode(' ', $filter->search->value) as $key => $string) {
                    $qb = $qb
                        ->andWhere('i.date LIKE :search_' . $key . ' OR i.description LIKE :search_' . $key . ' OR i.type LIKE :search_' . $key)
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
                $qb = $qb->setMaxResults($filter->length)
                    ->setFirstResult($filter->start);
            }

            /** @var \App\Entity\Document\Factura\FacturaFeed[] $list */
            $list = $qb->getQuery()->getResult();

            $datatable = new DataTable();
            $datatable->recordsTotal = count($total->getQuery()->getResult());
            foreach ($list as $item) {
                $datatable->data[] = $item->model(FacturaFeed::class)->json();
            }
            $datatable->recordsFiltered = count($total->getQuery()->getResult());

            return new  Http\JsonResponse($datatable);

        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @param \App\Entity\Document\Factura\Factura $item
     * @return void
     * @throws LoaderError
     * @throws ModuleException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function notifyCreated(\App\Entity\Document\Factura\Factura $item): void
    {
        // Buscar receptores de la notificación
        $employees = self::getNotificationReceivers(\App\Enum\Role::RECEIVE_EMAIL_CREATE->value, $item);

        // Preparar mensaje y enviar
        foreach ($employees as $employee) {
            $body = Twig::render('mail/partial/new-invoice.html.twig', ['item' => $item, 'title' => 'Nueva factura']);
            $text = Twig::renderFromString($body);
            $mail = Mail::getInstance();
            $mail->addEmbeddedImage(FileSystem::getPath(Directory::PUBLIC) . '/img/logo/logo-blue.png', 'logo');
            Mail::send($employee->email, 'Nueva factura en molletexpress', ['html' => $body, 'text' => $text]);
        }

    }

    /**
     * @param \App\Entity\Document\Factura\Factura $item
     * @return void
     * @throws LoaderError
     * @throws ModuleException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function notifyEdited(\App\Entity\Document\Factura\Factura $item): void
    {
        // Buscar receptores de la notificación
        $employees = self::getNotificationReceivers(\App\Enum\Role::RECEIVE_EMAIL_EDIT->value, $item);

        // Preparar mensaje y enviar
        foreach ($employees as $employee) {
            $body = Twig::render('mail/partial/new-invoice.html.twig', ['item' => $item, 'title' => 'Factura actualizada']);
            $text = Twig::renderFromString($body);


            $mail = Mail::getInstance();

            $mail->addEmbeddedImage(FileSystem::getPath(Directory::PUBLIC) . '/img/logo/logo-blue.png', 'logo');
            Mail::send($employee->email, 'Factura actualizada en molletexpress', ['html' => $body, 'text' => $text]);
        }
    }

    /**
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws ModuleException
     */
    public static function notifyDeleted(\App\Entity\Document\Factura\Factura $item): void
    {
        // Buscar receptores de la notificación
        $employees = self::getNotificationReceivers(\App\Enum\Role::RECEIVE_EMAIL_DELETE->value, $item);

        // Preparar mensaje y enviar
        foreach ($employees as $employee) {
            $body = Twig::render('mail/partial/new-invoice.html.twig', ['item' => $item, 'title' => 'Factura eliminada']);
            $text = Twig::renderFromString($body);
            $mail = Mail::getInstance();
            $mail->addEmbeddedImage(FileSystem::getPath(Directory::PUBLIC) . '/img/logo/logo-blue.png', 'logo');
            Mail::send($employee->email, 'Factura eliminada en molletexpress', ['html' => $body, 'text' => $text]);
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

    public static function getFeed(int $id): Http\JsonResponse
    {
        try {

            $item = \App\Entity\Document\Factura\FacturaFeed::findOneBy(['id' => $id, 'deletedOn' => null]);

            return new Http\JsonResponse($item->model(FacturaFeed::class)->json());
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function getAlert(int $id): Http\JsonResponse
    {
        try {

            $item = \App\Entity\Document\Factura\FacturaFeedAlert::findOneBy(['feed' => $id, 'deletedOn' => null]);

            if ($item)
                return new Http\JsonResponse($item->model(FacturaFeedAlert::class)->json());

            return new Http\JsonResponse(new FacturaFeedAlert());
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function add(): Http\JsonResponse
    {
        try {

            $model = \App\Model\Document\Factura\Factura::fromRequest();

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

            /** @var \App\Entity\Document\Factura\Factura $entity */
            $entity = $model->entity();

            $em = DB::getManager();

            foreach ($entity->contacts as $contact) {
                $contact->factura = $entity;
                $em->persist($contact);

                // Evitamos el bucle infinito
                $contact->factura->contacts = [];
            }

            $entity->employee->roles = [];
            if ($entity->employee->comercial) {
                $entity->employee->comercial->roles = [];
            }

            $em->persist($entity);
            $em->flush();


            try {
                self::notifyCreated($entity);
            } catch (Exception $e) {

            }

            return new Http\JsonResponse([
                'message' => "Factura añadida correctamente",
                'item' => $entity->model(\App\Model\Document\Factura\Factura::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function addFeed(): Http\JsonResponse
    {
        try {

            $model = FacturaFeed::fromRequest();

            if (is_string($model->factura->employee->logo))
                $model->factura->employee->logo = \App\Enum\Images::from($model->factura->employee->logo);
            if (is_string($model->factura->employee->icono))
                $model->factura->employee->icono = \App\Enum\Images::from($model->factura->employee->icono);
            if (is_string($model->factura->employee->fondo))
                $model->factura->employee->fondo = \App\Enum\Images::from($model->factura->employee->fondo);

            if ($model->factura->employee->comercial) {
                if (is_string($model->factura->employee->comercial->logo))
                    $model->factura->employee->comercial->logo = \App\Enum\Images::from($model->factura->employee->comercial->logo);
                if (is_string($model->factura->employee->comercial->icono))
                    $model->factura->employee->comercial->icono = \App\Enum\Images::from($model->factura->employee->comercial->icono);
                if (is_string($model->factura->employee->comercial->fondo))
                    $model->factura->employee->comercial->fondo = \App\Enum\Images::from($model->factura->employee->comercial->fondo);
            }

            $entity = $model->entity();

            $em = DB::getManager();
            $em->persist($entity);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Feed añadido correctamente",
                'item' => $entity->model(FacturaFeed::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function addAlert(): Http\JsonResponse
    {
        try {

            $model = FacturaFeedAlert::fromRequest();

            if (!$model->date)
                return new Http\JsonResponse(['message' => 'No se ha recibido todos los datos requeridos *'], 400);

            if (is_string($model->feed->factura->employee->logo))
                $model->feed->factura->employee->logo = \App\Enum\Images::from($model->feed->factura->employee->logo);
            if (is_string($model->feed->factura->employee->icono))
                $model->feed->factura->employee->icono = \App\Enum\Images::from($model->feed->factura->employee->icono);
            if (is_string($model->feed->factura->employee->fondo))
                $model->feed->factura->employee->fondo = \App\Enum\Images::from($model->feed->factura->employee->fondo);

            if ($model->feed->factura->employee->comercial) {
                if (is_string($model->feed->factura->employee->comercial->logo))
                    $model->feed->factura->employee->comercial->logo = \App\Enum\Images::from($model->feed->factura->employee->comercial->logo);
                if (is_string($model->feed->factura->employee->comercial->icono))
                    $model->feed->factura->employee->comercial->icono = \App\Enum\Images::from($model->feed->factura->employee->comercial->icono);
                if (is_string($model->feed->factura->employee->comercial->fondo))
                    $model->feed->factura->employee->comercial->fondo = \App\Enum\Images::from($model->feed->factura->employee->comercial->fondo);
            }

            $entity = $model->entity();

            $em = DB::getManager();
            $em->persist($entity);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Alert añadido correctamente",
                'item' => $entity->model(FacturaFeedAlert::class)->json()
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
            $item = \App\Entity\Document\Factura\Factura::findOneBy(['id' => $id, 'deletedOn' => null]);

            if (!$item)
                return new Http\JsonResponse(['message' => "Factura no encontrada"], 404);

            $model = \App\Model\Document\Factura\Factura::fromRequest();

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

            /** @var \App\Entity\Document\Factura\Factura $entity */
            $entity = $model->entity();
            $em = DB::getManager();
            foreach ($entity->contacts as $contact) {
                $contact->factura = $entity;
                $em->persist($contact);

                // Evitamos el bucle infinito
                $contact->factura->contacts = [];
            }

            $entity->employee->roles = [];
            if ($entity->employee->comercial) {
                $entity->employee->comercial->roles = [];
            }

            $em->persist($entity);
            $em->flush();
            try {
                self::notifyEdited($entity);
            } catch (Exception $e) {

            }

            return new Http\JsonResponse([
                'message' => "Factura actualizada correctamente",
                'item' => $entity->model(\App\Model\Document\Factura\Factura::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function updateFeed(int $id): Http\JsonResponse
    {
        try {

            $item = \App\Entity\Document\Factura\FacturaFeed::findOneBy(['id' => $id, 'deletedOn' => null]);

            if (!$item)
                return new Http\JsonResponse(['message' => "Feed no encontrado"], 404);

            $model = FacturaFeed::fromRequest();

            if ($id != $model->id)
                return new Http\JsonResponse(['message' => "La id indicada en la url no corresponde a la enviada en el modelo"], 404);

            if (is_string($model->factura->employee->logo))
                $model->factura->employee->logo = \App\Enum\Images::from($model->factura->employee->logo);
            if (is_string($model->factura->employee->icono))
                $model->factura->employee->icono = \App\Enum\Images::from($model->factura->employee->icono);
            if (is_string($model->factura->employee->fondo))
                $model->factura->employee->fondo = \App\Enum\Images::from($model->factura->employee->fondo);

            if ($model->factura->employee->comercial) {
                if (is_string($model->factura->employee->comercial->logo))
                    $model->factura->employee->comercial->logo = \App\Enum\Images::from($model->factura->employee->comercial->logo);
                if (is_string($model->factura->employee->comercial->icono))
                    $model->factura->employee->comercial->icono = \App\Enum\Images::from($model->factura->employee->comercial->icono);
                if (is_string($model->factura->employee->comercial->fondo))
                    $model->factura->employee->comercial->fondo = \App\Enum\Images::from($model->factura->employee->comercial->fondo);
            }

            $entity = $model->entity();

            $em = DB::getManager();
            $em->persist($entity);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Feed actualizado correctamente",
                'item' => $entity->model(FacturaFeed::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function updateAlert(int $id): Http\JsonResponse
    {
        try {

            $item = \App\Entity\Document\Factura\FacturaFeedAlert::findOneBy(['id' => $id, 'deletedOn' => null]);

            if (!$item)
                return new Http\JsonResponse(['message' => "Alert no encontrado"], 404);

            $model = FacturaFeedAlert::fromRequest();

            if (!$model->date)
                return new Http\JsonResponse(['message' => 'No se ha recibido todos los datos requeridos *'], 400);
            if ($id != $model->id)
                return new Http\JsonResponse(['message' => "La id indicada en la url no corresponde a la enviada en el modelo"], 404);

            if (is_string($model->feed->factura->employee->logo))
                $model->feed->factura->employee->logo = \App\Enum\Images::from($model->feed->factura->employee->logo);
            if (is_string($model->feed->factura->employee->icono))
                $model->feed->factura->employee->icono = \App\Enum\Images::from($model->feed->factura->employee->icono);
            if (is_string($model->feed->factura->employee->fondo))
                $model->feed->factura->employee->fondo = \App\Enum\Images::from($model->feed->factura->employee->fondo);

            if ($model->feed->factura->employee->comercial) {
                if (is_string($model->feed->factura->employee->comercial->logo))
                    $model->feed->factura->employee->comercial->logo = \App\Enum\Images::from($model->feed->factura->employee->comercial->logo);
                if (is_string($model->feed->factura->employee->comercial->icono))
                    $model->feed->factura->employee->comercial->icono = \App\Enum\Images::from($model->feed->factura->employee->comercial->icono);
                if (is_string($model->feed->factura->employee->comercial->fondo))
                    $model->feed->factura->employee->comercial->fondo = \App\Enum\Images::from($model->feed->factura->employee->comercial->fondo);
            }

            $entity = $model->entity();
            $em = DB::getManager();
            $em->persist($entity);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Alert actualizado correctamente",
                'item' => $entity->model(FacturaFeedAlert::class)->json()
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

            try {
                self::notifyDeleted($item);
            } catch (Exception $e) {

            }

            return new Http\JsonResponse([
                'message' => "Factura eliminada correctamente",
                'item' => $item->model(\App\Model\Document\Factura\Factura::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function deleteFeed(int $id): Http\JsonResponse
    {
        try {

            $em = DB::getManager();

            $item = \App\Entity\Document\Factura\FacturaFeed::findOneBy(['id' => $id, 'deletedOn' => null]);

            $item->deletedOn = new DateTime();

            $em->persist($item);
            $em->flush();

            return new Http\JsonResponse(['message' => "Feed eliminado correctamente"]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @param string $roleReceive
     * @param \App\Entity\Document\Factura\Factura $item
     * @return array
     * @throws ModuleException
     */
    public static function getNotificationReceivers(string $roleReceive, \App\Entity\Document\Factura\Factura $item): array
    {
        $employees = [];
        foreach (\App\Entity\Employee\Employee::findBy(['deletedOn' => null]) as $employee) {
            $canReceiver = false;
            // Comprobar si puede recibir notificación de facturas creados/actualizados/borrados
            // y si le corresponde la factura (Si puede verlo)
            foreach ($employee->roles as $role) {
                if ($role->role->value == $roleReceive && $role->group->value === 'INVOICE' && $role->active)
                    $canReceiver = true;
            }
            if ($canReceiver) {
                $model = \App\Model\Auth::getEmployee();
                $canReceiver = false;
                foreach ($employee->roles as $role) {
                    if ($role->role->value == 'VIEW_ALL' && $role->group->value === 'INVOICE' && $role->active)
                        $canReceiver = true;
                }
                if ($canReceiver) {
                    $employees[] = $employee;
                } elseif ($item->employee) {
                    if ($employee->id == $item->employee->id)
                        $employees[] = $employee;
                    elseif ($model->comercial) {
                        if ($employee->id == $model->comercial->id)
                            $employees[] = $employee;
                    }
                } elseif ($model->comercial) {
                    if ($employee->id == $model->comercial->id)
                        $employees[] = $employee;
                }
            }
        }
        return $employees;
    }
}