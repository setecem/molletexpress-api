<?php

namespace App\Controller;

use App\Entity\User\UserType;
use Cavesman\Db;
use Cavesman\Http;
use DateTime;
use Doctrine\ORM\Exception\ORMException;
use Exception;

class User
{
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

            //TODO Buscar en user_department qué líneas hay asociadas al usuario y borrarlas

            return new Http\JsonResponse(['message' => "Usuario eliminado correctamente"]);
        } catch (Exception | ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }
}