<?php

namespace App\Model\Contact;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;
use DateTime;

class ContactFeedAlert extends Model
{

    const string|Base ENTITY = \App\Entity\Contact\ContactFeedAlert::class;
    public ?int $id = 0;
    public ?ContactFeed $feed = null;
    public bool $active = true;
    public ?string $description = null;
    public DateTime|string|null $date = null;

    public function typeOfCollection(string $property): ?string
    {
        return match($property) {
            'feed' => ContactFeed::class,
            default => null
        };
    }

}