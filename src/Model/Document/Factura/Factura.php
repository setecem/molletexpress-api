<?php

namespace App\Model\Document\Factura;

use App\Model\Client;
use App\Model\Document\Documento;
use App\Model\OrdenCobro;
use Cavesman\Db\Doctrine\Entity\Base;

class Factura extends Documento
{

    const string|Base ENTITY = \App\Entity\Document\Factura\Factura::class;

    public ?OrdenCobro $ordenCobro = null;

    public function typeOfCollection(string $property): ?string
    {
        return match ($property) {
            'client' => Client::class,
            'ordenCobro' => OrdenCobro::class,
            'lineas' => FacturaLinea::class,
            default => null
        };
    }

}