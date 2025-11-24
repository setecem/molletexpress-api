<?php

namespace App\Model\Contact;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;
use DateTime;

class ContactFeed  extends Model
{

    const string|Base ENTITY = \App\Entity\Contact\ContactFeed::class;
    public ?int $id = 0;
    public ?Contact $contact = null;
    public ?string $type = null;
    public ?string $description = null;
    public DateTime|string|null $date = null;
    public bool $private = true;

    public function typeOfCollection(string $property): ?string
    {
        return match($property) {
            'contact' => Contact::class,
            default => null
        };
    }

}