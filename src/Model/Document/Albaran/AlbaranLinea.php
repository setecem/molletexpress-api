<?php

namespace App\Model\Document\Albaran;

use App\Entity\Document\Albaran\Albaran;
use App\Entity\Document\Factura\FacturaLinea;
use App\Model\Document\DocumentoLinea;

class AlbaranLinea extends DocumentoLinea
{

    public Albaran $albaran;

    public FacturaLinea $facturaLinea;

}