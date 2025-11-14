<?php

namespace App\Model;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;

class Service extends Model
{

    const string|Base ENTITY = \App\Entity\Service::class;

    public ?string $ref = null;
    public ?string $name = null;
    public ?string $measurementsUnit = null;
    public int $price = 0;
    public int $cost = 0;
    public bool $active = true;

}