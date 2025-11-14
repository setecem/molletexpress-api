<?php

namespace App\Entity;

use Cavesman\Db\Doctrine\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Table(name: 'service')]
#[ORM\Entity]
class Service extends Entity
{

    #[ORM\Column(name: 'reference', type: 'string', nullable: true)]
    public ?string $ref = null;

    #[ORM\Column(name: 'name', type: 'string', nullable: true)]
    public ?string $name = null;

    #[ORM\Column(name: 'measurements_unit', type: 'string', nullable: true)]
    public ?string $measurementsUnit = null;

    #[ORM\Column(name: 'price', type: 'integer', nullable: false, options: ['default' => '0'])]
    public int $price = 0;

    #[ORM\Column(name: 'cost', type: 'integer', nullable: false, options: ['default' => '0'])]
    public int $cost = 0;

    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => true])]
    public bool $active = true;

}