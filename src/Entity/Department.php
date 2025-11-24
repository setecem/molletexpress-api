<?php

namespace App\Entity;

use App\Entity\Contact\Contact;
use App\Entity\Employee\Employee;
use App\Entity\User\User;
use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Table(name: 'department')]
#[ORM\Entity]
class Department extends Entity
{

    #[ORM\Column(name: 'name', type: 'string', length: 50, nullable: false)]
    public string $name;

    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => true])]
    public bool $active = true;

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'department')]
    #[ORM\OrderBy(['id' => 'DESC'])]
    public array|Collection $users;


    #[ORM\Column(name: 'date_created', type: 'datetime', nullable: true)]
    public ?DateTime $dateCreated = null;

    #[ORM\Column(name: 'date_modified', type: 'datetime', nullable: true)]
    public ?DateTime $dateModified = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

}
