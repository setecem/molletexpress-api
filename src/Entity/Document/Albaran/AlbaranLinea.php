<?php

namespace App\Entity\Document\Albaran;

use App\Entity\Document\DocumentLine;
use App\Entity\Document\Factura\FacturaLinea;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'albaran_linea')]
#[ORM\Entity]
class AlbaranLinea extends DocumentLine
{
    #[ORM\JoinColumn(name: 'albaran', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Albaran::class)]
    public ?Albaran $albaran = null;

    #[ORM\JoinColumn(name: 'factura_linea', referencedColumnName: 'id')]
    #[ORM\OneToOne(targetEntity: FacturaLinea::class, inversedBy: 'factura_linea')]
    public ?FacturaLinea $facturaLinea = null;

}