<?php

namespace App\Entity;

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

    #[ORM\OneToOne(targetEntity: Employee::class, mappedBy: 'user')]
    public ?Employee $employee = null;

    #[ORM\JoinColumn(name: 'client_id', referencedColumnName: 'id', nullable: true)]
    #[ORM\ManyToOne(targetEntity: Client::class)]
    public ?Client $client = null;

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

    #[ORM\Column(name: 'contract', type: 'integer', nullable: false, options: ['default' => 0])]
    public int $contract = 0;

    #[ORM\Column(name: 'hours', type: 'integer', nullable: false, options: ['default' => 0])]
    public int $totalHours = 0;

    #[ORM\Column(name: 'phone', type: 'string', nullable: true)]
    public ?string $phone = null;

    #[ORM\Column(name: 'mobile', type: 'string', nullable: true)]
    public ?string $mobile = null;

    #[ORM\Column(name: 'email', type: 'string', nullable: true)]
    public ?string $email = null;

    #[ORM\Column(name: 'username', type: 'string', nullable: true)]
    public ?string $username = null;

    #[ORM\Column(name: 'password', type: 'string', nullable: true)]
    public ?string $password = null;

    #[ORM\Column(name: 'token', type: 'string', nullable: true)]
    public ?string $token = null;

    #[ORM\Column(name: 'date_start', type: 'datetime', nullable: true)]
    public ?DateTime $dateStart = null;

    #[ORM\Column(name: 'date_end', type: 'datetime', nullable: true)]
    public ?DateTime $dateEnd = null;

    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => true])]
    public bool $active = true;

    #[ORM\OneToMany(targetEntity: UserHours::class, mappedBy: 'user')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    public array|Collection $hours;

    public function __construct()
    {
        $this->hours = new ArrayCollection();
    }
}
