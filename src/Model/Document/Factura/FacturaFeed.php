<?php

namespace App\Model\Document\Factura;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;
use DateTime;

class FacturaFeed  extends Model
{

    const string|Base ENTITY = \App\Entity\Document\Factura\FacturaFeed::class;
    public ?int $id = 0;
    public ?Factura $factura = null;
    public ?string $type = null;
    public ?string $description = null;
    public DateTime|string|null $date = null;
    public bool $private = true;

    public function typeOfCollection(string $property): ?string
    {
        return match($property) {
            'factura' => Factura::class,
            default => null
        };
    }

}