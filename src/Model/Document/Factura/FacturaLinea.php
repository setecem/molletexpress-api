<?php

namespace App\Model\Document\Factura;

use App\Model\Document\DocumentoLinea;

class FacturaLinea extends DocumentoLinea
{
    public ?Factura $factura = null;

}