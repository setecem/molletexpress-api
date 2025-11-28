<?php

namespace App\Entity\Document;

use Cavesman\Db\Doctrine\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;

abstract class DocumentLine extends Entity
{

    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'reference', type: 'string', nullable: false)]
    public ?string $reference = null;

    #[ORM\Column(name: 'unidad_medida', type: 'string', nullable: false)]
    public string $unidadMedida;

    #[ORM\Column(name: 'description', type: 'text', nullable: false)]
    public string $description;

    #[ORM\Column(name: 'quantity', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $quantity = 0;

    #[ORM\Column(name: 'discount', type: 'decimal', precision: 5, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $discount = 0;

    #[ORM\Column(name: 'quantity_certificada', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $quantityCertificada = 0;

    #[ORM\Column(name: 'price', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $price = 0;

    #[ORM\Column(name: 'tax', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $tax = 0;

    #[ORM\Column(name: 'total', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $total = 0;


}