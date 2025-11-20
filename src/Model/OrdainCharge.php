<?php

namespace App\Model;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;
use DateTime;

class OrdainCharge extends Model
{

    const string|Base ENTITY = \App\Entity\OrdainCharge::class;

    public ?string $ref = null;
    public ?Customer $customer = null;
    public int $amount = 0;
    public DateTime|string|null $date = null;


    public function typeOfCollection(string $property): ?string
    {
        return match($property) {
            'customer' => Customer::class,
            default => null
        };
    }
}