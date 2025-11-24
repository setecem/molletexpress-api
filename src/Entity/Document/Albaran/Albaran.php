<?php

namespace App\Entity\Document\Albaran;

use App\Entity\Document\Document;
use App\Entity\Employee\Employee;
use App\Entity\File;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'albaran')]
#[ORM\Entity]
class Albaran extends Document
{

    // Clonados de contact

    #[ORM\JoinColumn(name: 'employee_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Employee::class, cascade: ['persist'])]
    public ?Employee $employee = null;

    #[ORM\Column(name: 'enterprise', type: 'string', nullable: true)]
    public ?string $enterprise = null;

    #[ORM\Column(name: 'enterprise_phone', type: 'string', nullable: true)]
    public ?string $enterprise_phone = null;

    #[ORM\Column(name: 'enterprise_phone_ext', type: 'string', nullable: true)]
    public ?string $enterprise_phone_ext = null;

    #[ORM\Column(name: 'enterprise_mobile', type: 'string', nullable: true)]
    public ?string $enterprise_mobile = null;

    #[ORM\Column(name: 'enterprise_mobile_ext', type: 'string', nullable: true)]
    public ?string $enterprise_mobile_ext = null;

    #[ORM\Column(name: 'enterprise_email', type: 'string', nullable: true)]
    public ?string $enterprise_email = null;

    #[ORM\Column(name: 'sector', type: 'string', nullable: true)]
    public ?string $sector = null;

    #[ORM\Column(name: 'facturacion', type: 'string', nullable: true)]
    public ?string $facturacion = null;

    #[ORM\Column(name: 'web', type: 'string', nullable: true)]
    public ?string $web = null;

    #[ORM\Column(name: 'nif', type: 'string', nullable: true)]
    public ?string $nif = null;

    #[ORM\Column(name: 'address', type: 'string', nullable: true)]
    public ?string $address = null;

    #[ORM\Column(name: 'authorization', type: 'boolean', nullable: false, options: ['default' => '0'])]
    public bool $authorization = false;

    #[ORM\Column(name: 'type', type: 'string', nullable: true)]
    public ?string $type = null;

    #[ORM\Column(name: 'origen', type: 'string', nullable: true)]
    public ?string $origen = null;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    public ?string $description = null;

    #[ORM\Column(name: 'has_recursos', type: 'boolean', nullable: false, options: ['default' => '0'])]
    public bool $has_recursos = false;

    #[ORM\Column(name: 'multiexpedicion', type: 'boolean', nullable: false, options: ['default' => '0'])]
    public bool $multiexpedicion = false;

    #[ORM\Column(name: 'almacenes', type: 'string', nullable: true)]
    public ?string $almacenes = null;

    #[ORM\Column(name: 'muelles', type: 'string', nullable: true)]
    public ?string $muelles = null;

    #[ORM\Column(name: 'recursos', type: 'string', nullable: true)]
    public ?string $recursos = null;

    /** @var AlbaranContact[]|Collection */
    #[ORM\OneToMany(targetEntity: AlbaranContact::class, mappedBy: 'albaran')]
    public array|Collection $contacts = [];

    /** @var File[]|Collection */
    #[ORM\OneToMany(targetEntity: File::class, mappedBy: 'albaran')]
    public array|Collection $files = [];

    #[ORM\Column(name: 'contact', type: 'string', nullable: true)]
    public ?string $contact = null;

    #[ORM\Column(name: 'contact_phone', type: 'string', nullable: true)]
    public ?string $contactPhone = null;

    #[ORM\Column(name: 'contact_email', type: 'string', nullable: true)]
    public ?string $contactEmail = null;

    #[ORM\Column(name: 'deleted', type: 'boolean', nullable: false, options: ['default' => '0'])]
    public bool $deleted = false;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->files = new ArrayCollection();
    }

}
