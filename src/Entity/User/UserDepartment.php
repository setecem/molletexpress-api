<?php

namespace App\Entity\User;

use App\Entity\Department;
use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'user_department')]
#[ORM\UniqueConstraint(name: 'user_department', columns: ['department_id', 'user_id'])]
#[ORM\Entity]
class UserDepartment extends Entity
{

    #[ORM\JoinColumn(name: 'department_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'users')]
    public Department $department;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'departments')]
    public User $user;

    #[ORM\Column(name: 'date_created', type: 'datetime', nullable: true)]
    public ?DateTime $dateCreated = null;

    #[ORM\Column(name: 'date_modified', type: 'datetime', nullable: true)]
    public ?DateTime $dateModified = null;

}