<?php

namespace App\Model;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;
use DateTime;

class OrdenCobro extends Model
{

    const string|Base ENTITY = \App\Entity\OrdenCobro::class;

    public ?string $ref = null;
    public ?Client $client = null;
    public int $amount = 0;
    public DateTime|string|null $date = null;


    public function typeOfCollection(string $property): ?string
    {
        return match($property) {
            'client' => Client::class,
            default => null
        };
    }
}