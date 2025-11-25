<?php

namespace App\Entity\Employee;

use App\Entity\File;
use App\Entity\User\User;
use App\Entity\User\UserDepartment;
use App\Entity\User\UserHours;
use App\Entity\User\UserType;
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
#[ORM\UniqueConstraint(name: 'email', columns: ['email'])]
#[ORM\Entity]
class Employee extends Entity
{

    #[ORM\Column(name: 'name', type: 'string', nullable: false)]
    public ?string $name = null;

    #[ORM\Column(name: 'lastname', type: 'string', nullable: true)]
    public ?string $lastname = null;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\OneToOne(targetEntity: User::class)]
    public ?User $user = null;

    #[ORM\Column(name: 'dni', type: 'string', nullable: false)]
    public ?string $dni = null;

    #[ORM\Column(name: 'salario_base', type: 'decimal', precision: 10, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $salarioBase = 0.0;

    #[ORM\Column(name: 'coste_fijo', type: 'decimal', precision: 10, scale: 2, nullable: true, options: ['default' => 0.0])]
    public float $costeFijo = 0.0;

    #[ORM\Column(name: 'despido_30_dias', type: 'decimal', precision: 10, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $despido30Dias = 0.0;

    #[ORM\Column(name: 'salario_mensual_nominal', type: 'decimal', precision: 10, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $salarioMensualNominal = 0.0;

    #[ORM\Column(name: 'plus_extra', type: 'decimal', precision: 10, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $plusExtra = 0.0;

    #[ORM\Column(name: 'precio_hora', type: 'decimal', precision: 10, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $precioHora = 0.0;

    #[ORM\Column(name: 'precio_hora_extra', type: 'decimal', precision: 10, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $precioHoraExtra = 0.0;

    #[ORM\Column(name: 'precio_hora_festivo', type: 'decimal', precision: 10, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $precioHoraFestivo = 0.0;

    #[ORM\Column(name: 'plus_guardias', type: 'decimal', precision: 10, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $plusGuardias = 0.0;

    #[ORM\Column(name: 'adelantos', type: 'decimal', precision: 10, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $adelantos = 0.0;

    #[ORM\Column(name: 'retenciones', type: 'decimal', precision: 10, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $retenciones = 0.0;

    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => true])]
    public bool $active = true;

    #[ORM\Column(name: 'date_created', type: 'datetime', nullable: true)]
    public ?DateTime $dateCreated = null;

    #[ORM\Column(name: 'date_modified', type: 'datetime', nullable: true)]
    public ?DateTime $dateModified = null;

    /** Estaban en User */

    #[ORM\Column(name: 'nif', type: 'string', nullable: true)]
    public ?string $nif = null;

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
    public ?string $email = null;

    #[ORM\Column(name: 'username', type: 'string', length: 50, nullable: false)]
    public ?string $username = null;

    #[ORM\Column(name: 'password', type: 'string', nullable: false)]
    public ?string $password = null;

    #[ORM\Column(name: 'token', type: 'string', nullable: true)]
    public ?string $token = null;

    #[ORM\Column(name: 'date_start', type: 'datetime', nullable: true)]
    public ?DateTime $dateStart = null;

    #[ORM\Column(name: 'date_end', type: 'datetime', nullable: true)]
    public ?DateTime $dateEnd = null;

    #[ORM\OneToMany(targetEntity: UserDepartment::class, mappedBy: 'user')]
    #[ORM\OrderBy(['id' => 'DESC'])]
    public array|Collection $departments;

    #[ORM\OneToMany(targetEntity: UserHours::class, mappedBy: 'user')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    public array|Collection $hours;

    public array|Collection $actions;

    public array|Collection $children;

    /** Campos traidos del CRM */

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
        $this->hours = new ArrayCollection();
        $this->actions = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->departments = new ArrayCollection();
    }

}
