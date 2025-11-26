<?php

namespace App\Model\Document\Albaran;

use App\Entity\Document\Albaran\AlbaranLinea;
use App\Model\Client;
use App\Model\Document\DocumentoLinea;
use App\Model\Document\Factura\Factura;
use App\Model\Employee\Employee;
use App\Model\File;
use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;
use DateTime;

class Albaran extends Model
{

    const string|Base ENTITY = \App\Entity\Document\Albaran\Albaran::class;

    public ?Factura $factura = null;
}