<?php

namespace App\Model\Document\Factura;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;
use DateTime;

class FacturaFeedAlert extends Model
{

    const string|Base ENTITY = \App\Entity\Document\Factura\FacturaFeedAlert::class;
    public ?int $id = 0;
    public ?FacturaFeed $feed = null;
    public bool $active = true;
    public ?string $description = null;
    public DateTime|string|null $date = null;

    public function typeOfCollection(string $property): ?string
    {
        return match($property) {
            'feed' => FacturaFeed::class,
            default => null
        };
    }

}