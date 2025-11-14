<?php

namespace App\Entity;

use App\Enum\RoleGroup;
use Cavesman\Db\Doctrine\Entity\Base;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'role')]
class Role extends Base
{

    #[ORM\Id]
    #[ORM\Column(name: 'role', type: 'enum', enumType: \App\Enum\Role::class)]
    public \App\Enum\Role $role = \App\Enum\Role::ACCESS;

    #[ORM\Id]
    #[ORM\Column(name: '`group`', type: 'enum', enumType: RoleGroup::class)]
    public RoleGroup $group = RoleGroup::EMPLOYEE;

    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => true])]
    public bool $active = true;

}
