<?php

namespace App\Model;

use App\Enum\RoleGroup;
use BackedEnum;
use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Interface\Model;

class Role extends \Cavesman\Db\Doctrine\Model\Model implements Model
{

    const string|Base ENTITY = \App\Entity\Role::class;
    public \App\Enum\Role $role = \App\Enum\Role::ACCESS;
    public RoleGroup $group = RoleGroup::EMPLOYEE;
    public bool $active = true;

    public function typeOfEnum($name): BackedEnum|string|null
    {
        return match ($name) {
            'group' => RoleGroup::class,
            'role' => \App\Enum\Role::class,
            default => null
        };
    }
}
