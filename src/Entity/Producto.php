<?php

namespace App\Entity;

use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'producto')]
#[ORM\Entity]
class Producto extends Entity
{

    #[ORM\Column(name: 'name', type: 'string', nullable: false)]
    public string $name;

    #[ORM\Column(name: 'reference', type: 'string', nullable: true)]
    public ?string $reference = null;

    #[ORM\Column(name: 'date', type: 'datetime', nullable: true)]
    public ?DateTime $date = null;

    #[ORM\Column(name: 'cost', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $cost = 0;

    #[ORM\Column(name: 'price', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $price = 0;

    #[ORM\Column(name: 'tax', type: 'string', nullable: false, options: ['default' => '21'])]
    public string $tax = '21';

    #[ORM\Column(name: 'unidad_medida', type: 'string', nullable: false)]
    public string $unidadMedida;

    #[ORM\Column(name: 'active', type: 'boolean', options: ['default' => true])]
    public bool $active = true;

    #[ORM\Column(name: 'date_created', type: 'datetime', nullable: true)]
    public ?DateTime $dateCreated = null;

    #[ORM\Column(name: 'date_modified', type: 'datetime', nullable: true)]
    public ?DateTime $dateModified = null;
}