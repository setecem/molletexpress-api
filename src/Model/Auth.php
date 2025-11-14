<?php

namespace App\Model;

use App\Entity\Employee;
use Cavesman\Db;
use Cavesman\Exception\ModuleException;
use Cavesman\JWT;
use Cavesman\Request;
use Exception;
use stdClass;

class Auth extends Db\Doctrine\Model\Model
{
    private static ?\App\Model\Employee $info = null;
    public ?string $username = null;
    public ?string $password = null;

    /**
     * @throws ModuleException
     * @throws Exception
     */
    public static function getEmployee(): \App\Model\Employee
    {

        if (self::$info)
            return self::$info;

        $token = substr(Request::header('Authorization', ''), 7);

        if (!$token)
            throw new Exception('auth.error.jwt.not-found');

        $decode = JWT::decode($token);

        $item = Db::getManager()->getRepository(Employee::class)->findOneBy(['id' => $decode->id, 'deletedOn' => null]);

        if (!$item)
            throw new Exception('auth.error.employee.not-found');

        self::$info = $item->model(\App\Model\Employee::class);

        return self::$info;
    }

    public static function getData(): ?stdClass
    {
        if (PHP_SAPI === 'cli')
            return null;
        $token = substr(Request::header('Authorization'), 7);

        if (!$token)
            return null;

        return JWT::decode($token);
    }
}
