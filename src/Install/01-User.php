<?php

use Cavesman\Console;
use Cavesman\Enum\Console\Type;
use Doctrine\ORM\Exception\ORMException;

try {
    $em = \Cavesman\Db::getManager();
    $admin = \App\Entity\Employee\Employee::findOneBy(['username' => 'admin']);
    if(!$admin) {
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

