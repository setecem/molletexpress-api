<?php

use App\Entity\Employee\Employee;
use Cavesman\Console;
use Cavesman\Db;
use Cavesman\Enum\Console\Type;
use Doctrine\ORM\Exception\ORMException;

try {
    $em = Db::getManager();
    $admin = Employee::findOneBy(['username' => 'admin']);
    if (!$admin) {
        $admin = new App\Model\Employee\Employee([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => password_hash('1234', PASSWORD_DEFAULT),
            'active' => true,
            'email' => 'pruebas@setecem.com'
        ]);
        $entity = $admin->entity();
        $em->persist($entity);
    }
    $em->flush();

} catch (Exception|ORMException $e) {
    Console::output($e->getMessage(), Type::WARNING);
    Console::output($e->getTraceAsString(), Type::ERROR);
    exit();
}

