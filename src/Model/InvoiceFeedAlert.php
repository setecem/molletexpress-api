<?php

namespace App\Model;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;
use DateTime;

class InvoiceFeedAlert extends Model
{

    const string|Base ENTITY = \App\Entity\InvoiceFeedAlert::class;
    public ?int $id = 0;
    public ?InvoiceFeed $feed = null;
    public bool $active = true;
    public ?string $description = null;
    public DateTime|string|null $date = null;

    public function typeOfCollection(string $property): ?string
    {
        return match($property) {
            'feed' => InvoiceFeed::class,
            default => null
        };
    }

}