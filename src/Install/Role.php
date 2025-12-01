<?php

use App\Entity\Employee\Employee;
use App\Entity\Employee\EmployeeRole;
use App\Entity\Role;
use App\Enum\RoleGroup;
use Cavesman\Console;
use Cavesman\Db;
use Cavesman\Enum\Console\Type;
use Doctrine\ORM\Exception\ORMException;

try {
    $em = Db::getManager();

    $admin = Employee::findOneBy(['username' => 'admin']);
    foreach (array_merge(RoleGroup::rolesEmployee(), RoleGroup::rolesClient(), RoleGroup::rolesInvoice(), RoleGroup::rolesDeliveryNote(), RoleGroup::rolesService(), RoleGroup::rolesChargeOrder()) as $groupName => $roles) {
        $roleGroup = RoleGroup::from($groupName);
        foreach ($roles as $role) {
            $item = $em->getRepository(Role::class)->findOneBy(['role' => $role, 'group' => $roleGroup]);

            if (!$item)
                $item = new Role();

            $item->role = $role;
            $item->group = $roleGroup;

            $em->persist($item);

            $employeeRole = EmployeeRole::findOneBy(['employee' => $admin, 'role' => $role, 'group' => $roleGroup]);

            if(!$employeeRole) {
                $employeeRole = new EmployeeRole();
                $employeeRole->employee = $admin;
                $employeeRole->role = $role;
                $employeeRole->group = $roleGroup;
                $employeeRole->active = true;
                $em->persist($employeeRole);
            }
        }
    }
    $em->flush();

} catch (Exception|ORMException $e) {
    Console::output($e->getMessage(), Type::WARNING);
    Console::output($e->getTraceAsString(), Type::ERROR);
    exit();
}

