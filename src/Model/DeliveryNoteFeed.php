<?php

namespace App\Model;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;
use DateTime;

class DeliveryNoteFeed  extends Model
{

    const string|Base ENTITY = \App\Entity\DeliveryNoteFeed::class;
    public ?int $id = 0;
    public ?DeliveryNote $deliveryNote = null;
    public ?string $type = null;
    public ?string $description = null;
    public DateTime|string|null $date = null;
    public bool $private = true;

    public function typeOfCollection(string $property): ?string
    {
        return match($property) {
            'deliveryNote' => DeliveryNote::class,
            default => null
        };
    }

}