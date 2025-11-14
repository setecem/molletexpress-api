<?php

namespace App\Model;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;
use DateTime;

class OrdainCharge extends Model
{

    const string|Base ENTITY = \App\Entity\OrdainCharge::class;

    public ?string $ref = null;
    public ?Client $client = null;
    public int $amount = 0;
    public ?DateTime $date = null;


    public function typeOfCollection(string $property): ?string
    {
        return match($property) {
            'client' => Client::class,
            default => null
        };
    }
}