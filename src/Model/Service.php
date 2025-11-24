<?php

namespace App\Model;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;

class Service extends Model
{

    const string|Base ENTITY = \App\Entity\Service::class;

    public ?string $ref = null;
    public ?string $name = null;
    public ?string $unidadMedida = null;
    public float $price = 0.0;
    public float $cost = 0.0;
    public bool $active = true;

}