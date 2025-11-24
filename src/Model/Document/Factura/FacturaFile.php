<?php

namespace App\Model\Document\Factura;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;

class FacturaFile extends Model
{

    const string|Base ENTITY = \App\Entity\Document\Factura\FacturaFile::class;
    public ?string$extension = null;
    public ?string $name = null;
    public ?Factura $factura = null;
    public int $size = 0;
    public ?string $type = null;
    public ?string $mimetype = null;
    public bool $active = true;
    public bool $private = true;

    public function typeOfCollection(string $property): ?string
    {
        return match($property) {
            'factura' => Factura::class,
            default => null
        };
    }


}