<?php

namespace App\Entity\Document\Factura;

use Cavesman\Db\Doctrine\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'factura_contact')]
#[ORM\Entity]
class FacturaContact extends Entity
{
    #[ORM\JoinColumn(name: 'factura_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Factura::class, inversedBy: 'contacts')]
    public ?Factura $factura = null;

    #[ORM\Column(name: 'name', type: 'string', nullable: true)]
    public ?string $name = null;

    #[ORM\Column(name: 'cargo', type: 'string', nullable: true)]
    public ?string $cargo = null;

    #[ORM\Column(name: 'phone', type: 'string', nullable: true)]
    public ?string $phone = null;

    #[ORM\Column(name: 'phone_ext', type: 'string', nullable: true)]
    public ?string $phone_ext = null;

    #[ORM\Column(name: 'mobile', type: 'string', nullable: true)]
    public ?string $mobile = null;

    #[ORM\Column(name: 'mobile_ext', type: 'string', nullable: true)]
    public ?string $mobile_ext = null;

    #[ORM\Column(name: 'email', type: 'string', nullable: true)]
    public ?string $email = null;

    #[ORM\Column(name: 'main', type: 'boolean', nullable: false, options: ['default' => false])]
    public bool $main = false;

    #[ORM\Column(name: 'address', type: 'string', nullable: true)]
    public ?string $address = null;
    
}
