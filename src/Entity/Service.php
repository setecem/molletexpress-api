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

    #[ORM\Column(name: 'unidad_medida', type: 'string', nullable: true)]
    public ?string $unidadMedida = null;

    #[ORM\Column(name: 'price', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $price = 0.0;

    #[ORM\Column(name: 'cost', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $cost = 0.0;

    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => true])]
    public bool $active = true;

}