<?php

namespace App\Entity\Document\Factura;

use App\Entity\Document\Albaran\Albaran;
use App\Entity\Document\Albaran\AlbaranLinea;
use App\Entity\Document\DocumentLine;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'factura_linea')]
#[ORM\Entity]
class FacturaLinea extends DocumentLine
{
    #[ORM\JoinColumn(name: 'factura', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Factura::class)]
    public ?Factura $factura = null;

    #[ORM\JoinColumn(name: 'albaran', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Albaran::class)]
    public ?Albaran $albaran = null;

    #[ORM\JoinColumn(name: 'albaran_linea', referencedColumnName: 'id')]
    #[ORM\OneToOne(targetEntity: AlbaranLinea::class)]
    public ?AlbaranLinea $albaranLinea = null;
}