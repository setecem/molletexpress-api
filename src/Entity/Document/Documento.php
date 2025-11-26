<?php

namespace App\Entity\Document;

use App\Entity\Client;
use App\Enum\DocumentStatus;
use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

abstract class Documento extends Entity
{

    #[ORM\Column(name: 'status', type: 'enum', nullable: false, enumType: DocumentStatus::class, options: ['default' => DocumentStatus::DRAFT])]
    public DocumentStatus $status = DocumentStatus::DRAFT;

    #[ORM\Column(name: 'serie', type: 'string', nullable: false, options: ['default' => 'P'])]
    public string $serie = 'P';

    #[ORM\Column(name: 'reference', type: 'integer', nullable: true)]
    public ?int $reference = null;

    #[ORM\Column(name: 'num_pedido', type: 'integer', nullable: true, options: ['default' => 0])]
    public int|null $numPedido = 0;

    #[ORM\Column(name: 'number', type: 'string', nullable: true)]
    public ?string $number = null;

    #[ORM\Column(name: 'date', type: 'datetime', nullable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
    public DateTime $date;

    #[ORM\Column(name: 'observaciones', type: 'text', nullable: true)]
    public ?string $observaciones = null;

    #[ORM\JoinColumn(name: 'client', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Client::class)]
    public ?Client $client = null;

    #[ORM\Column(name: 'importe_bruto', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $importeBruto = 0;

    #[ORM\Column(name: 'subtotal', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $subtotal = 0;

    #[ORM\Column(name: 'tax', type: 'string', nullable: false, options: ['default' => '21'])]
    public string $tax = '21';

    #[ORM\Column(name: 'total', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $total = 0;

    #[ORM\Column(name: 'pagado', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $pagado = 0;

    #[ORM\Column(name: 'balance', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $balance = 0;

    #[ORM\Column(name: 'discount', type: 'decimal', precision: 5, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $discount = 0;

    #[ORM\Column(name: 'discount_pp', type: 'decimal', precision: 5, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $discountPP = 0;

    #[ORM\Column(name: 'imp_discount', type: 'decimal', precision: 5, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $impDiscount = 0;

    #[ORM\Column(name: 'imp_discount_pp', type: 'decimal', precision: 5, scale: 2, nullable: false, options: ['default' => '0.00'])]
    public float $impDiscountPP = 0;

    #[ORM\Column(name: 'comments', type: 'text', nullable: true)]
    public ?string $comments = null;

    #[ORM\Column(name: 'pagada', type: 'boolean', nullable: false, options: ['default' => false])]
    public bool $pagada = false;

    #[ORM\Column(name: 'bloqueada', type: 'boolean', nullable: false, options: ['default' => true])]
    public bool $bloqueada = true;


}