<?php

namespace App\Controller;

use App\Entity\Employee\EmployeeRole;
use App\Entity\User\UserType;
use App\Enum\RoleGroup;
use Cavesman\Console;
use Cavesman\Db;
use Cavesman\Enum\Console\Type;
use Cavesman\Http;
use DateTime;
use Doctrine\ORM\Exception\ORMException;
use Exception;

class User
{

    public static function updatePassword(): void
    {
        try {
            $username = Console::requestValue('Escribe el nombre de usuario:');

            $item = \App\Entity\User\User::findOneBy(['username' => $username]);

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

            $users = \App\Entity\User\User::findBy([]);

            if (sizeof($users)) {
                foreach ($users as $user) {
                    $entity = \App\Entity\User\User::findOneBy(['user' => $user->id]);
                    if (!$entity)
                        $entity = new \App\Entity\User\User();
                    $entity->createdOn = $user->dateCreated;
                    $entity->updatedOn = $user->dateModified;
                    $em->persist($entity);
                }
                $em->flush();

                $employee = \App\Entity\User\User::findOneBy(['name' => 'admin']);

                if (!$employee) {
                    $employee = new \App\Entity\User\User();
                    $employee->firstname = 'Administrador';
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

            $list = \App\Entity\User\User::findBy(['deletedOn' => null]);

            return new Http\JsonResponse(array_map(fn(\App\Entity\User\User $user) => $user->model(\App\Model\User\User::class)->json(), $list));
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function get(int $id): Http\JsonResponse
    {
        try {

            $item = \App\Entity\User\User::findOneBy(['id' => $id, 'deletedOn' => null]);

            return new Http\JsonResponse($item->model(\App\Model\User\User::class)->json());
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function active(int $id): Http\JsonResponse
    {
        try {

            $item = \App\Entity\User\User::findOneBy(['id' => $id, 'deletedOn' => null]);

            $item->active = !$item->active;

            $em = DB::getManager();
            $em->persist($item);
            $em->flush();

            if ($item->active)
                $return['message'] = "Usuario activado correctamente";
            else
                $return['message'] = "Usuario desactivado correctamente";

            return new Http\JsonResponse($return);
        } catch (Exception | ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function add(): Http\JsonResponse
    {
        try {

            $model = \App\Model\User\User::fromRequest();

            $model->password = password_hash($model->password, PASSWORD_DEFAULT);

            /** @var \App\Entity\User\User $entity */
            $entity = $model->entity();
            $em = DB::getManager();

            if ($entity->employee) {
                $entity->type = UserType::findOneBy(['id' => 1]);
                $entity->employee->user = $entity;
                $em->persist($entity->employee);
                $entity->employee = null;
            } elseif ($entity->client)
                $entity->type = UserType::findOneBy(['id' => 3]);
            else
                $entity->type = UserType::findOneBy(['id' => 2]);

            $em->persist($entity->type);
            $em->persist($entity);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Usuario añadido correctamente",
                'item' => $entity->model(\App\Model\User\User::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage(), 'exception' => $e->getTrace()], 500);
        }
    }

    public static function update(int $id): Http\JsonResponse
    {
        try {

            $item = \App\Entity\User\User::findOneBy(['id' => $id, 'deletedOn' => null]);

            if (!$item)
                return new Http\JsonResponse(['message' => "Usuario no encontrado"], 404);

            $model = \App\Model\User\User::fromRequest();

            if ($id != $model->id)
                return new Http\JsonResponse(['message' => "La id indicada en la url no corresponde a la enviada en el modelo"], 404);

            if ($item->password != $model->password)
                $model->password = password_hash($model->password, PASSWORD_DEFAULT);

            /** @var \App\Entity\User\User $entity */
            $entity = $model->entity();
            $em = DB::getManager();

            if ($entity->employee) {
                $entity->employee->user = $entity;
                $em->persist($entity->employee);
                $entity->employee = null;
            }

            $em->persist($entity);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Usuario actualizado correctamente",
                'item' => $entity->model(\App\Model\User\User::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }


    public static function delete(int $id): Http\JsonResponse
    {
        try {

            $item =  \App\Entity\User\User::findOneBy(['id' => $id, 'deletedOn' => null]);

            $item->deletedOn = new DateTime();

            $em = DB::getManager();
            $em->persist($item);
            $em->flush();

            return new Http\JsonResponse(['message' => "Usuario eliminado correctamente"]);
        } catch (Exception | ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }
}