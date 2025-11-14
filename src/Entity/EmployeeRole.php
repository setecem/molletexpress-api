<?php

namespace App\Entity;
use App\Enum\Role;
use App\Enum\RoleGroup;
use Cavesman\Db\Doctrine\Entity\Base;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'employee_role')]
class EmployeeRole extends Base
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Employee::class, cascade: ['persist'], inversedBy: 'roles')]
    #[ORM\JoinColumn(name: 'employee_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    public ?Employee $employee = null;

    #[ORM\Id]
    #[ORM\Column(name: 'role', type: 'enum', enumType: Role::class)]
    public Role $role = Role::ACCESS;

    #[ORM\Id]
    #[ORM\Column(name: '`group`', type: 'enum', enumType: RoleGroup::class)]
    public RoleGroup $group = RoleGroup::EMPLOYEE;

    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => true])]
    public bool $active = true;
}