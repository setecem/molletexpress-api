<?php

namespace App\Entity;

use App\Entity\Contact\Contact;
use App\Entity\Employee\Employee;
use App\Entity\User\User;
use Cavesman\Db\Doctrine\Entity\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Table(name: 'department')]
#[ORM\Entity]
class Department extends Entity
{

    #[ORM\Column(name: 'name', type: 'string', nullable: true)]
    public ?string $name = null;

    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => true])]
    public bool $active = true;

    #[ORM\JoinTable(name: 'user_department')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'department_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'departments')]
    public array|Collection $users;

    #[ORM\JoinTable(name: 'employee_department')]
    #[ORM\JoinColumn(name: 'department_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'employee_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Employee::class, inversedBy: 'departments')]
    public array|Collection $employees;

    #[ORM\JoinTable(name: 'contact_department')]
    #[ORM\JoinColumn(name: 'department_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'contact_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Contact::class, inversedBy: 'departments')]
    public array|Collection $contacts;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->employees = new ArrayCollection();
        $this->contacts = new ArrayCollection();
    }

}
