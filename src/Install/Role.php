<?php

use App\Entity\Role;
use App\Enum\RoleGroup;
use Cavesman\Console;
use Cavesman\Db;
use Cavesman\Enum\Console\Type;
use Doctrine\ORM\Exception\ORMException;

try {
    $em = Db::getManager();
    foreach (array_merge(RoleGroup::rolesEmployee(), RoleGroup::rolesContact(), RoleGroup::rolesInvoice(), RoleGroup::rolesDeliveryNote(), RoleGroup::rolesService(), RoleGroup::rolesOrdainCharge()) as $groupName => $roles) {
        $roleGroup = RoleGroup::from($groupName);
        foreach ($roles as $role) {
            $item = $em->getRepository(Role::class)->findOneBy(['role' => $role, 'group' => $roleGroup]);

            if (!$item)
                $item = new Role();

            $item->role = $role;
            $item->group = $roleGroup;

            $em->persist($item);
        }
    }
    $em->flush();

} catch (Exception|ORMException $e) {
    Console::output($e->getMessage(), Type::WARNING);
    Console::output($e->getTraceAsString(), Type::ERROR);
    exit();
}

