<?php

namespace App\Entity;

use App\Entity\Document\Albaran\Albaran;
use App\Entity\Document\Factura\Factura;
use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'orden_cobro')]
#[ORM\Entity]
class OrdenCobro extends Entity
{

    #[ORM\Column(name: 'reference', type: 'string', nullable: true)]
    public ?string $reference = null;

    #[ORM\JoinColumn(name: 'client_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Client::class, cascade: ['persist'])]
    public ?Client $client = null;

    #[ORM\Column(name: 'date', type: 'datetime', nullable: true)]
    public ?DateTime $date = null;

    #[ORM\Column(name: 'active', type: 'boolean', options: ['default' => false])]
    public bool $active = false;

    #[ORM\Column(name: 'pagada', type: 'boolean', nullable: false, options: ['default' => false])]
    public bool $pagada = false;

    #[ORM\Column(name: 'date_created', type: 'datetime', nullable: true)]
    public ?DateTime $dateCreated = null;

    #[ORM\Column(name: 'date_modified', type: 'datetime', nullable: true)]
    public ?DateTime $dateModified = null;

    /** @var Factura[]|Collection */
    #[ORM\OneToMany(targetEntity: Factura::class, mappedBy: 'orden')]
    public array|Collection $facturas = [];


}
