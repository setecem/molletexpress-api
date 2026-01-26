<?php

namespace App\Entity;

use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Table(name: 'producto')]
#[ORM\Entity]
class Service extends Entity
{

    #[ORM\Column(name: 'name', type: 'string', nullable: true)]
    public ?string $name = null;

    #[ORM\Column(name: 'reference', type: 'string', unique: true, nullable: true)]
    public ?string $ref = null;

    #[ORM\Column(name: 'date', type: 'datetime', nullable: true)]
    public ?DateTime $date = null;

    #[ORM\Column(name: 'cost', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $cost = 0;

    #[ORM\Column(name: 'price', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $price = 0;

    #[ORM\Column(name: 'tax', type: 'string', nullable: false, options: ['default' => '21'])]
    public string $tax = '21';

    #[ORM\Column(name: 'unidad_medida', type: 'string', nullable: true)]
    public ?string $unidadMedida = null;

    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => true])]
    public bool $active = true;

    #[ORM\Column(name: 'date_created', type: 'datetime', nullable: true)]
    public ?DateTime $dateCreated = null;

    #[ORM\Column(name: 'date_modified', type: 'datetime', nullable: true)]
    public ?DateTime $dateModified = null;
}