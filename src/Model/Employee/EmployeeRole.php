<?php

namespace App\Model\Employee;

use App\Enum\Role;
use App\Enum\RoleGroup;
use BackedEnum;
use Cavesman\Db\Doctrine\Model\Base;


class EmployeeRole extends Base
{
    const string|\Cavesman\Db\Doctrine\Entity\Base ENTITY = \App\Entity\Employee\EmployeeRole::class;
    public ?Employee $employee = null;
    public Role $role = Role::ACCESS;
    public RoleGroup $group = RoleGroup::EMPLOYEE;
    public bool $active = true;

    public function typeOfEnum($name): BackedEnum|string|null
    {
        return match ($name) {
            'group' => RoleGroup::class,
            'role' => Role::class,
            'employee' => Employee::class,
            default => null
        };
    }
}
