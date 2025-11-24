<?php

namespace App\Model\Document\Albaran;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;
use DateTime;

class AlbaranFeedAlert extends Model
{

    const string|Base ENTITY = \App\Entity\Document\Albaran\AlbaranFeedAlert::class;
    public ?int $id = 0;
    public ?AlbaranFeed $feed = null;
    public bool $active = true;
    public ?string $description = null;
    public DateTime|string|null $date = null;

    public function typeOfCollection(string $property): ?string
    {
        return match($property) {
            'feed' => AlbaranFeed::class,
            default => null
        };
    }

}