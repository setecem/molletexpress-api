<?php

namespace App\Model\Document\Albaran;

use App\Model\Document\DocumentoLinea;
use App\Model\Document\Factura\FacturaLinea;
use Cavesman\Db\Doctrine\Entity\Base;

class AlbaranLinea extends DocumentoLinea
{

    const string|Base ENTITY = \App\Entity\Document\Albaran\AlbaranLinea::class;

    public ?Albaran $albaran = null;

    public ?FacturaLinea $facturaLinea = null;

}