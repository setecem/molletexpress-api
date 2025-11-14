<?php

namespace App\Model;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;

class DeliveryNoteContact extends Model
{

    const string|Base ENTITY = \App\Entity\DeliveryNoteContact::class;
    public ?int $id = 0;
    public ?string $name = null;
    public ?string $cargo = null;
    public ?string $phone = null;
    public ?string $phone_ext = null;
    public ?string $mobile = null;
    public ?string $mobile_ext = null;
    public ?string $email = null;
    public bool $main = false;
    public ?string $address = null;

    public function typeOfCollection(string $property): ?string
    {
        return match ($property) {
            'deliveryNote' => DeliveryNote::class,
            default => null
        };
    }

}