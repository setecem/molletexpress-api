<?php

namespace App\Controller;

use App\Entity\Employee\EmployeeRole;
use App\Entity\User\User;
use App\Enum\RoleGroup;
use App\Model\DataTable;
use App\Model\Employee\EmployeeBase;
use Cavesman\Console;
use Cavesman\Db;
use Cavesman\Enum\Console\Type;
use Cavesman\Http;
use Cavesman\Request;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Exception\ORMException;
use Exception;

class Employee
{
    public static function updatePassword(): void
    {
        try {
            $username = Console::requestValue('Escribe el nombre de usuario:');

            $item = \App\Entity\Employee\Employee::findOneBy(['username' => $username]);

            if (!$item)
                Console::output('ERROR: Username not found');
            else {
                 $password = Console::requestValue('Escribe una nueva constraseña:');

                 $item->password = password_hash($password, PASSWORD_DEFAULT);

                 $em = Db::getManager();
                 $em->persist($item);
                 $em->flush();
            }

        } catch (Exception|ORMException $e) {
            Console::output($e->getMessage(), Type::WARNING);
            Console::output($e->getTraceAsString(), Type::ERROR);
            exit();
        }
    }
    public static function migrateUsers(): void
    {
        try {
            $em = Db::getManager();

            $users = User::findBy([]);

            if (sizeof($users)) {
                foreach ($users as $user) {
                    $entity = \App\Entity\Employee\Employee::findOneBy(['user' => $user->id]);
                    if ($entity) {
                        $entity->username = $user->username;
                        $entity->password = $user->password;
                    } else {
                        $entity = new \App\Entity\Employee\Employee();
                        $entity->name = $user->firstname;
                        $entity->lastname = $user->lastname;
                        $entity->email = $user->email;
                        $entity->username = $user->username;
                        $entity->password = $user->password;
                        $entity->createdOn = $user->dateCreated;
                        $entity->updatedOn = $user->dateModified;
                    }
                    $em->persist($entity);
                }
                $em->flush();

                $employee = \App\Entity\Employee\Employee::findOneBy(['username' => 'admin']);

                if (!$employee) {
                    $employee = new \App\Entity\Employee\Employee();
                    $employee->name = 'Administrador';
                    $employee->lastname = 'General';
                    $employee->username = 'admin';
                    $employee->password = password_hash('1234', PASSWORD_DEFAULT);
                    $em->persist($employee);
                    $em->flush();
                }

                    foreach (array_merge(RoleGroup::rolesEmployee(), RoleGroup::rolesClient(), RoleGroup::rolesContact(), RoleGroup::rolesInvoice(), RoleGroup::rolesDeliveryNote(), RoleGroup::rolesService(), RoleGroup::rolesOrdainCharge()) as $groupName => $roles) {
                    $group = RoleGroup::from($groupName);
                    foreach ($roles as $item) {
                        $employeeRole = $em->getRepository(EmployeeRole::class)->findOneBy(['employee' => $employee, 'role' => $item, 'group' => $group]);
                        if (!$employeeRole)
                            $employeeRole = new EmployeeRole();
                        $employeeRole->employee = $employee;
                        $employeeRole->role = $item;
                        $employeeRole->group = $group;
                        $em->persist($employeeRole);
                    }
                }
                $em->flush();
            }
        } catch (Exception|ORMException $e) {
            Console::output($e->getMessage(), Type::WARNING);
            Console::output($e->getTraceAsString(), Type::ERROR);
            exit();
        }

    }

    public static function list(): Http\JsonResponse
    {
        try {

            $list = \App\Entity\Employee\Employee::findBy(['deletedOn' => null]);

            return new Http\JsonResponse(array_map(fn(\App\Entity\Employee\Employee $employee) => $employee->model(EmployeeBase::class)->json(), $list));
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function filterAll(): Http\JsonResponse
    {
        try {
            $em = Db::getManager();

            $qb = $em->getRepository(\App\Entity\Employee\Employee::class)
                ->createQueryBuilder('i')
                ->where('i.deletedOn IS NULL');

            $filter = json_decode(Request::get('filter', '[]'));

            if ($filter && $filter->search) {
                foreach (explode(' ', $filter->search->value) as $key => $string) {
                    $qb = $qb
                        ->andWhere('i.name LIKE :search_' . $key . ' OR i.lastname LIKE :search_' . $key . ' OR i.id LIKE :search_' . $key)
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

            /** @var \App\Entity\Employee\Employee[] $list */
            $list = $qb->getQuery()->getResult();

            $datatable = new DataTable();
            $datatable->recordsTotal = count($total->getQuery()->getResult());
            foreach ($list as $item) {
                /** @var \App\Model\Employee\Employee $model */
                $model = $item->model(EmployeeBase::class);
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

            $qb = $em->getRepository(\App\Entity\Employee\Employee::class)
                ->createQueryBuilder('i')
                ->where('i.deletedOn IS NULL')
                ->andWhere('i.id = :id')
                ->setParameter('id', $id);

            $filter = json_decode(Request::get('filter', '[]'));

            if ($filter && $filter->search) {
                foreach (explode(' ', $filter->search->value) as $key => $string) {
                    $qb = $qb
                        ->andWhere('i.name LIKE :search_' . $key . ' OR i.lastname LIKE :search_' . $key . ' OR i.id LIKE :search_' . $key)
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

            /** @var \App\Entity\Employee\Employee[] $list */
            $list = $qb->getQuery()->getResult();

            $datatable = new DataTable();
            $datatable->recordsTotal = count($total->getQuery()->getResult());
            foreach ($list as $item) {
                /** @var \App\Model\Employee\Employee $model */
                $model = $item->model(EmployeeBase::class);
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

            $item = \App\Entity\Employee\Employee::findOneBy(['id' => $id, 'deletedOn' => null]);

            return new Http\JsonResponse($item->model(EmployeeBase::class)->json());
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function active(int $id): Http\JsonResponse
    {
        try {

            $em = DB::getManager();

            $item = \App\Entity\Employee\Employee::findOneBy(['id' => $id, 'deletedOn' => null]);

            $item->active = !$item->active;

            $em->persist($item);
            $em->flush();

            if ($item->active)
                $return['message'] = "Empleado activado correctamente";
            else
                $return['message'] = "Empleado desactivado correctamente";

            return new Http\JsonResponse($return);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function add(): Http\JsonResponse
    {
        try {

            $model = \App\Model\Employee\Employee::fromRequest();

            if (!$model->username || !$model->password || !$model->name)
                return new Http\JsonResponse(['message' => 'No se ha recibido todos los datos requeridos'], 400);

            $item = \App\Entity\Employee\Employee::findOneBy(['username' => $model->username]);

            if ($item)
                return new Http\JsonResponse(['message' => 'Este usuario ya existe'], 400);

            $model->password = password_hash($model->password, PASSWORD_DEFAULT);password_hash($model->password, PASSWORD_DEFAULT);

            if (is_string($model->logo))
                $model->logo = \App\Enum\Images::from($model->logo);
            if (is_string($model->icono))
                $model->icono = \App\Enum\Images::from($model->icono);
            if (is_string($model->fondo))
                $model->fondo = \App\Enum\Images::from($model->fondo);

            if ($model->comercial) {
                if (is_string($model->comercial->logo))
                    $model->comercial->logo = \App\Enum\Images::from($model->comercial->logo);
                if (is_string($model->comercial->icono))
                    $model->comercial->icono = \App\Enum\Images::from($model->comercial->icono);
                if (is_string($model->comercial->fondo))
                    $model->comercial->fondo = \App\Enum\Images::from($model->comercial->fondo);
            }

            /** @var \App\Entity\Employee\Employee $entity */
            $entity = $model->entity();

            if ($entity->comercial)
                $entity->comercial->roles = [];

            $roles = $entity->roles;
            $entity->roles = [];

            $em = DB::getManager();
            $em->persist($entity);

            foreach ($roles as $role) {
                $role->employee = $entity;
                $em->persist($role);
            }

            $em->persist($entity);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Empleado añadido correctamente",
                'item' => $entity->model(\App\Model\Employee\Employee::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function update(int $id): Http\JsonResponse
    {
        try {

            $item = \App\Entity\Employee\Employee::findOneBy(['id' => $id, 'deletedOn' => null]);

            if (!$item)
                return new Http\JsonResponse(['message' => "Empleado no encontrado"], 404);

            $model = \App\Model\Employee\Employee::fromRequest();

            if (!$model->username || !$model->name)
                return new Http\JsonResponse(['message' => 'No se ha recibido todos los datos requeridos'], 400);

            if ($id != $model->id)
                return new Http\JsonResponse(['message' => "La id indicada en la url no corresponde a la enviada en el modelo"], 404);

            if (is_string($model->logo))
                $model->logo = \App\Enum\Images::from($model->logo);
            if (is_string($model->icono))
                $model->icono = \App\Enum\Images::from($model->icono);
            if (is_string($model->fondo))
                $model->fondo = \App\Enum\Images::from($model->fondo);

            if ($model->comercial) {
                if (is_string($model->comercial->logo))
                    $model->comercial->logo = \App\Enum\Images::from($model->comercial->logo);
                if (is_string($model->comercial->icono))
                    $model->comercial->icono = \App\Enum\Images::from($model->comercial->icono);
                if (is_string($model->comercial->fondo))
                    $model->comercial->fondo = \App\Enum\Images::from($model->comercial->fondo);
            }

            $em = DB::getManager();

            $otherItem = $em->createQueryBuilder()
                ->select('i')
                ->from(\App\Entity\Employee\Employee::class, 'i')
                ->where('i.id != :id AND i.deletedOn IS NULL AND i.username IS NOT NULL AND i.username = :username')
                ->setParameter('id', $id)
                ->setParameter('username', $model->username)
                ->getQuery()->getOneOrNullResult();

            if ($otherItem)
                return new Http\JsonResponse(['message' => 'Este usuario ya existe'], 400);

            if ($model->password && $item->password != $model->password)
                $model->password = password_hash($model->password, PASSWORD_DEFAULT);
            elseif (!$model->password)
                $model->password = $item->password;

            $em = DB::getManager();

            /** @var \App\Entity\Employee\Employee $entity */
            $entity = $model->entity();

            if ($entity->comercial)
                $entity->comercial->roles = new ArrayCollection();

            $roles = $entity->roles;
            $entity->roles = new ArrayCollection();
            foreach ($roles as $r) {
                $role = EmployeeRole::findOneBy(['employee' => $entity, 'role' => $r->role, 'group' => $r->group]);
                if (!$role) {
                    $r->employee = $entity;
                    $entity->roles->add($r);
                    $em->persist($r);
                } else {
                    $role->active = $r->active;
                    $em->persist($role);
                }
            }

            $em->persist($entity);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Empleado actualizado correctamente",
                'item' => $item->model(\App\Model\Employee\Employee::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }


    public static function delete(int $id): Http\JsonResponse
    {
        try {

            $item = \App\Entity\Employee\Employee::findOneBy(['id' => $id, 'deletedOn' => null]);

            $item->deletedOn = new DateTime();

            $em = DB::getManager();
            $em->persist($item);
            $em->flush();

            return new Http\JsonResponse(['message' => "Empleado eliminado correctamente"]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

}

