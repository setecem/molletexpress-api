<?php

namespace App\Entity\Document;

use App\Entity\Client;
use App\Entity\Document\Albaran\Albaran;
use App\Entity\Document\Factura\Factura;
use App\Entity\Document\Pedido\Pedido;
use App\Entity\Document\Presupuesto\Presupuesto;
use App\Entity\OrdenCobro;
use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

abstract class Document extends Entity
{

    #[ORM\Column(name: 'serie', type: 'string', nullable: false, options: ['default' => 'P'])]
    public string $serie = 'P';

    #[ORM\Column(name: 'reference', type: 'integer', nullable: false, options: ['default' => 0])]
    public int $reference = 0;

    #[ORM\Column(name: 'num_pedido', type: 'integer', nullable: true, options: ['default' => 0])]
    public int|null $numPedido = 0;

    #[ORM\Column(name: 'number', type: 'string', nullable: false)]
    public string $number;

    #[ORM\Column(name: 'date', type: 'datetime', nullable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
    public DateTime $date;

    #[ORM\Column(name: 'observaciones', type: 'text', nullable: true)]
    public ?string $observaciones = null;

    #[ORM\JoinColumn(name: 'client', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Client::class)]
    public ?Client $client = null;

    #[ORM\JoinColumn(name: 'orden', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: OrdenCobro::class)]
    public ?OrdenCobro $orden = null;

    #[ORM\Column(name: 'importe_bruto', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $importeBruto = 0.0;

    #[ORM\Column(name: 'subtotal', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $subtotal = 0.0;

    #[ORM\Column(name: 'tax', type: 'string', nullable: false, options: ['default' => '21'])]
    public string $tax = '21';

    #[ORM\Column(name: 'total', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $total = 0.0;

    #[ORM\Column(name: 'pagado', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $pagado = 0.0;

    #[ORM\Column(name: 'balance', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $balance = 0.0;

    #[ORM\Column(name: 'discount', type: 'decimal', precision: 5, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $discount = 0.0;

    #[ORM\Column(name: 'discount_pp', type: 'decimal', precision: 5, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $discountPP = 0.0;

    #[ORM\Column(name: 'imp_discount', type: 'decimal', precision: 5, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $impDiscount = 0.0;

    #[ORM\Column(name: 'imp_discount_pp', type: 'decimal', precision: 5, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $impDiscountPP = 0.0;

    #[ORM\Column(name: 'comments', type: 'text', nullable: true)]
    public ?string $comments = null;

    #[ORM\Column(name: 'pagada', type: 'boolean', nullable: false, options: ['default' => false])]
    public bool $pagada = false;

    #[ORM\Column(name: 'bloqueada', type: 'boolean', nullable: false, options: ['default' => true])]
    public bool $bloqueada = true;

    #[ORM\Column(name: 'date_created', type: 'datetime', nullable: true)]
    public ?DateTime $dateCreated = null;

    #[ORM\Column(name: 'date_modified', type: 'datetime', nullable: true)]
    public ?DateTime $dateModified = null;

    #[ORM\JoinColumn(name: 'presupuesto', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Presupuesto::class, inversedBy: 'presupuestos')]
    public Presupuesto $presupuesto;

    #[ORM\JoinColumn(name: 'pedido', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Pedido::class, inversedBy: 'pedidos')]
    public Pedido $pedido;

    #[ORM\JoinColumn(name: 'albaran', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Albaran::class, inversedBy: 'albaranes')]
    public Albaran $albaran;

    #[ORM\JoinColumn(name: 'factura', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Factura::class, inversedBy: 'facturas')]
    public Factura $factura;

}