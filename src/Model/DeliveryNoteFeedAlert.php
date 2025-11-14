<?php

namespace App\Model;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;
use DateTime;

class DeliveryNoteFeedAlert extends Model
{

    const string|Base ENTITY = \App\Entity\DeliveryNoteFeedAlert::class;
    public ?int $id = 0;
    public ?DeliveryNoteFeed $feed = null;
    public bool $active = true;
    public ?string $description = null;
    public DateTime|string|null $date = null;

    public function typeOfCollection(string $property): ?string
    {
        return match($property) {
            'feed' => DeliveryNoteFeed::class,
            default => null
        };
    }

}