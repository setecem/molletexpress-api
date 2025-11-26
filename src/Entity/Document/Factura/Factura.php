<?php

namespace App\Entity\Document\Factura;

use App\Entity\Document\Albaran\AlbaranLinea;
use App\Entity\Document\Documento;
use App\Entity\OrdenCobro;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'factura')]
#[ORM\Entity]
class Factura extends Documento
{

    /** @var FacturaLinea[]|Collection */
    #[ORM\OneToMany(targetEntity: FacturaLinea::class, mappedBy: 'factura')]
    public array|Collection $lines = [];

    #[ORM\JoinColumn(name: 'orden_cobro', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: OrdenCobro::class)]
    public ?OrdenCobro $ordenCobro = null;
}
