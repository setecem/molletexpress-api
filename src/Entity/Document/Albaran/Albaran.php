<?php

namespace App\Entity\Document\Albaran;

use App\Entity\Document\Documento;
use App\Entity\Document\Factura\Factura;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'albaran')]
#[ORM\Entity]
class Albaran extends Documento
{
    #[ORM\JoinColumn(name: 'factura', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Factura::class, inversedBy: 'facturas')]
    public ?Factura $factura = null;

    /** @var AlbaranLinea[]|Collection */
    #[ORM\OneToMany(targetEntity: AlbaranLinea::class, mappedBy: 'albaran', cascade: ['persist'])]
    public array|Collection $lineas = [];
}
