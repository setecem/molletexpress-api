<?php

namespace App\Entity\Employee;

use App\Entity\File;
use App\Entity\User\User;
use App\Entity\User\UserHours;
use App\Enum\Images;
use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'employee')]
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

    #[ORM\Column(name: 'dni', type: 'string', nullable: true)]
    public ?string $dni = null;

    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => true])]
    public bool $active = true;

    #[ORM\Column(name: 'email', type: 'string', nullable: false)]
    public ?string $email = null;

    #[ORM\Column(name: 'username', type: 'string', nullable: false)]
    public ?string $username = null;

    #[ORM\Column(name: 'password', type: 'string', nullable: false)]
    public ?string $password = null;

    #[ORM\Column(name: 'date_created', type: 'datetime', nullable: true)]
    public ?DateTime $dateCreated = null;

    #[ORM\Column(name: 'date_modified', type: 'datetime', nullable: true)]
    public ?DateTime $dateModified = null;

    /** Campos traidos de la web */
    #[ORM\Column(name: 'salario_base', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $salarioBase = 0;

    #[ORM\Column(name: 'salario_mensual_nominal', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $salarioMensualNominal = 0;

    #[ORM\Column(name: 'plus_extra', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $plusExtra = 0;

    #[ORM\Column(name: 'precio_hora', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $precioHora = 0;

    #[ORM\Column(name: 'precio_hora_extra', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $precioHoraExtra = 0;

    #[ORM\Column(name: 'precio_hora_festivo', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $precioHoraFestivo = 0;

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
    }

}
