<?php

namespace App\Model\Document\Albaran;

use App\Model\Client;
use App\Model\Document\Documento;
use App\Model\Document\Factura\Factura;
use Cavesman\Db\Doctrine\Entity\Base;

class Albaran extends Documento
{

    const string|Base ENTITY = \App\Entity\Document\Albaran\Albaran::class;

    public ?Factura $factura = null;

    public function typeOfCollection(string $property): ?string
    {
        return match ($property) {
            'client' => Client::class,
            'lineas' => AlbaranLinea::class,
            default => null
        };
    }
}