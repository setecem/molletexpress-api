<?php

namespace App\Model\Document\Factura;

use App\Model\Client;
use App\Model\Document\Documento;
use App\Model\Document\DocumentoLinea;
use Cavesman\Db\Doctrine\Entity\Base;

class Factura extends Documento
{

    const string|Base ENTITY = \App\Entity\Document\Factura\Factura::class;

}