<?php

namespace App\Entity;

use App\Enum\Images;
use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'employee')]
#[ORM\Index(name: 'token', columns: ['token'])]
#[ORM\Index(name: 'password', columns: ['password'])]
#[ORM\Index(name: 'username_password', columns: ['username', 'password'])]
#[ORM\Index(name: 'email_password', columns: ['email', 'password'])]
#[ORM\UniqueConstraint(name: 'username', columns: ['username'])]
// #[ORM\UniqueConstraint(name: 'email', columns: ['email'])] !!! --> No se puede hacer único en employee, ya que hay datos repetido.
#[ORM\Entity]
class Employee extends Entity
{

    #[ORM\Column(name: 'name', type: 'string', nullable: true)]
    public ?string $name = null;

    #[ORM\Column(name: 'lastname', type: 'string', nullable: true)]
    public ?string $lastname = null;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\OneToOne(targetEntity: User::class)]
    public ?User $user = null;

    #[ORM\Column(name: 'username', type: 'string', nullable: true)]
    public ?string $username = null;

    #[ORM\Column(name: 'password', type: 'string', nullable: true)]
    public ?string $password = null;

    #[ORM\Column(name: 'token', type: 'string', nullable: true)]
    public ?string $token = null;

    #[ORM\JoinColumn(name: 'comercial_id', referencedColumnName: 'id', nullable: true)]
    #[ORM\ManyToOne(targetEntity: Employee::class, cascade: ['persist'])]
    public ?Employee $comercial = null;

    #[ORM\Column(name: 'origen', type: 'string', nullable: true)]
    public ?string $origen = null;

    #[ORM\ManyToMany(targetEntity: Profile::class, mappedBy: 'employees')]
    public array|Collection $profiles;

    #[ORM\ManyToMany(targetEntity: Department::class, mappedBy: 'employees')]
    public array|Collection $departments;

    #[ORM\Column(name: 'dni', type: 'string', nullable: true)]
    public ?string $dni = null;

    #[ORM\Column(name: 'ss_num', type: 'string', nullable: true)]
    public ?string $ssNum = null;

    #[ORM\Column(name: 'ccc_num', type: 'string', nullable: true)]
    public ?string $cccNum = null;

    #[ORM\Column(name: 'phone', type: 'string', nullable: true)]
    public ?string $phone = null;

    #[ORM\Column(name: 'mobile', type: 'string', nullable: true)]
    public ?string $mobile = null;

    #[ORM\Column(name: 'email', type: 'string', nullable: true)]
    public ?string $email = null;

    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => true])]
    public bool $active = true;

    #[ORM\Column(name: 'need_comment', type: 'boolean', nullable: false, options: ['default' => false])]
    public bool $needComment = false;

    #[ORM\Column(name: 'date_start', type: 'datetime', nullable: true)]
    public ?DateTime $dateStart = null;

    #[ORM\Column(name: 'date_end', type: 'datetime', nullable: true)]
    public ?DateTime $dateEnd = null;

    #[ORM\Column(name: 'contract', type: 'integer', nullable: false, options: ['default' => 0])]
    public int $contract = 0;

    #[ORM\Column(name: 'hours', type: 'integer', nullable: false, options: ['default' => 0])]
    public int $hours = 0;

    /** @var File[]|Collection */
    #[ORM\OneToMany(targetEntity: File::class, mappedBy: 'employee')]
    public array|Collection $files = [];

    /** @var EmployeeRole[]|Collection */
    #[ORM\OneToMany(targetEntity: EmployeeRole::class, mappedBy: 'employee', cascade: ['persist'])]
    public array|Collection $roles = [];

    #[ORM\Column(name: 'logo', type: 'enum', nullable: false, enumType: Images::class, options: ['default' => Images::logoWhiteTextSetecem])]
    public Images $logo = Images::logoWhiteTextSetecem;

    #[ORM\Column(name: 'icono', type: 'enum', nullable: false, enumType: Images::class, options: ['default' => Images::logoWhiteSetecem])]
    public Images $icono = Images::logoWhiteSetecem;

    #[ORM\Column(name: 'fondo', type: 'enum', nullable: false, enumType: Images::class, options: ['default' => Images::bgSetecem])]
    public Images $fondo = Images::bgSetecem;

    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }

}
