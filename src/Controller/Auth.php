<?php

namespace App\Controller;

use App\Entity\Employee\Employee;
use Cavesman\Db;
use Cavesman\Http\JsonResponse;
use Cavesman\JWT;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Exception;

final class Auth
{

    /**
     * Authentication endpoint
     * @return JsonResponse
     * @throws Exception
     */
    public static function auth(): JsonResponse
    {
        try {
            $auth = \App\Model\Auth::fromRequest();
        } catch (Exception $e) {
            return new JsonResponse(['message' => 'Token invalido', 'exception' => $e->getMessage()], 500);
        }

        try {

            $em = Db::getManager();

            /** @var \App\Entity\Employee\Employee $item */
            $item = $em->getRepository(Employee::class)
                ->createQueryBuilder('e')
                ->where('e.username = :username AND e.active = :active AND e.deletedOn IS NULL')
                ->setParameters(new ArrayCollection([
                    new Parameter('username', $auth->username),
                    new Parameter('active', true)
                ]))
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if (!$item)
                throw new Exception('Usuario no encontrado');

            if (!password_verify($auth->password, $item->password))
                throw new Exception('Contraseña incorrecta');

            $token = JWT::encode(['id' => $item->id]);

            $employee = $item->model(\App\Model\Employee\Employee::class);

            return new JsonResponse(['employee' => $employee->json(), 'token' => $token, 'message' => 'Datos correctos'], 200);
        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }

    /**
     * MaintenanceCheck if token is valid
     *
     * @required Header Authorization
     * @return JsonResponse|void
     */
    public static function middleware()
    {
        try {
            \App\Model\Auth::getEmployee();
        } catch (Exception $e) {
            return new JsonResponse(['message' => 'Token invalido', 'exception' => $e->getMessage()], 400);
        }
    }

    /**
     * MaintenanceCheck if token is valid
     *
     * @required Header Authorization
     * @return JsonResponse
     */
    public static function check(): JsonResponse
    {

        try {

            try {
                $item = \App\Model\Auth::getEmployee();
            } catch (Exception) {
                throw new Exception('Token invalido');
            }

            return new JsonResponse(['message' => 'Token valido', 'employee' => $item->json()]);
        } catch (Exception $e) {
            return new JsonResponse(['message' => 'Token invalido', 'exception' => $e->getMessage()], 400);
        }
    }
}
