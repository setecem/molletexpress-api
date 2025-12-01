<?php

namespace App\Model\Document\Factura;

use App\Model\Document\DocumentoLinea;
use Cavesman\Db\Doctrine\Entity\Base;

class FacturaLinea extends DocumentoLinea
{
    const string|Base ENTITY = \App\Entity\Document\Factura\FacturaLinea::class;
    public ?Factura $factura = null;

}