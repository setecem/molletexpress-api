<?php

namespace App\Entity\User;

use App\Entity\Client;
use App\Entity\Employee\Employee;
use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'user')]
#[ORM\Index(name: 'token', columns: ['token'])]
#[ORM\Index(name: 'password', columns: ['password'])]
#[ORM\Index(name: 'username_password', columns: ['username', 'password'])]
#[ORM\Index(name: 'email_password', columns: ['email', 'password'])]
#[ORM\UniqueConstraint(name: 'username', columns: ['username'])]
#[ORM\UniqueConstraint(name: 'email', columns: ['email'])]
#[ORM\Entity]
class User extends Entity
{

    #[ORM\Column(name: 'nif', type: 'string', nullable: true)]
    public ?string $nif = null;

    #[ORM\Column(name: 'firstname', type: 'string', nullable: true)]
    public ?string $firstname = null;

    #[ORM\Column(name: 'lastname', type: 'string', nullable: true)]
    public ?string $lastname = null;

    #[ORM\Column(name: 'ss_num', type: 'string', nullable: true)]
    public ?string $ssNum = null;

    #[ORM\Column(name: 'ccc_num', type: 'string', nullable: true)]
    public ?string $cccNum = null;

    #[ORM\JoinColumn(name: 'type_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: UserType::class, cascade: ['persist'], inversedBy: 'users')]
    public ?UserType $type = null;

    #[ORM\Column(name: 'contract', type: 'integer', nullable: true, options: ['default' => 0])]
    public int $contract = 0;

    #[ORM\Column(name: 'hours', type: 'integer', nullable: false, options: ['default' => 0])]
    public int $totalHours = 0;

    #[ORM\Column(name: 'phone', type: 'string', length: 20, nullable: true)]
    public ?string $phone = null;

    #[ORM\Column(name: 'mobile', type: 'string', length: 20, nullable: true)]
    public ?string $mobile = null;

    #[ORM\Column(name: 'email', type: 'string', length: 100, nullable: false)]
    public string $email;

    #[ORM\Column(name: 'username', type: 'string', length: 50, nullable: false)]
    public string $username;

    #[ORM\Column(name: 'password', type: 'string', length: 50, nullable: false)]
    public string $password;

    #[ORM\Column(name: 'token', type: 'string', length: 64, nullable: false)]
    public string $token;

    #[ORM\Column(name: 'date_start', type: 'datetime', nullable: true)]
    public ?DateTime $dateStart = null;

    #[ORM\Column(name: 'date_end', type: 'datetime', nullable: true)]
    public ?DateTime $dateEnd = null;

    #[ORM\Column(name: 'date_created', type: 'datetime', nullable: true)]
    public ?DateTime $dateCreated = null;

    #[ORM\Column(name: 'date_modified', type: 'datetime', nullable: true)]
    public ?DateTime $dateModified = null;

    #[ORM\OneToMany(targetEntity: UserDepartment::class, mappedBy: 'user')]
    #[ORM\OrderBy(['id' => 'DESC'])]
    public array|Collection $departments;

    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => true])]
    public bool $active = true;

    #[ORM\OneToMany(targetEntity: UserHours::class, mappedBy: 'user')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    public array|Collection $hours;

    public array|Collection $actions;

    public array|Collection $children;

    /** TODO Estaban en crm, ¿Mantener aquí? */
    #[ORM\OneToOne(targetEntity: Employee::class, mappedBy: 'user')]
    public ?Employee $employee = null;

    #[ORM\JoinColumn(name: 'client_id', referencedColumnName: 'id', nullable: true)]
    #[ORM\ManyToOne(targetEntity: Client::class)]
    public ?Client $client = null;

    public function __construct()
    {
        $this->hours = new ArrayCollection();
    }
}
