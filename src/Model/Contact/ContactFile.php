<?php

namespace App\Model\Contact;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;

class ContactFile extends Model
{

    const string|Base ENTITY = \App\Entity\Contact\ContactFile::class;
    public ?string$extension = null;
    public ?string $name = null;
    public ?Contact $contact = null;
    public int $size = 0;
    public ?string $type = null;
    public ?string $mimetype = null;
    public bool $active = true;
    public bool $private = true;

    public function typeOfCollection(string $property): ?string
    {
        return match($property) {
            'contact' => Contact::class,
            default => null
        };
    }


}