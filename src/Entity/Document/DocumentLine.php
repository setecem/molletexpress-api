<?php

namespace App\Entity\Document;

use App\Entity\Document\Albaran\Albaran;
use App\Entity\Document\Albaran\AlbaranLinea;
use App\Entity\Document\Factura\Factura;
use App\Entity\Document\Factura\FacturaLinea;
use App\Entity\Document\Pedido\Pedido;
use App\Entity\Document\Presupuesto\Presupuesto;
use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

abstract class DocumentLine extends Entity
{

    #[ORM\Column(name: 'reference', type: 'string', nullable: false)]
    public string $reference;

    #[ORM\Column(name: 'unidad_medida', type: 'string', nullable: false)]
    public string $unidadMedida;

    #[ORM\Column(name: 'description', type: 'text', nullable: false)]
    public string $description;

    #[ORM\Column(name: 'quantity', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $quantity = 0.0;

    #[ORM\Column(name: 'discount', type: 'decimal', precision: 5, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $discount = 0.0;

    #[ORM\Column(name: 'quantity_certificada', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $quantityCertificada = 0.0;

    #[ORM\Column(name: 'price', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $price = 0.0;

    #[ORM\Column(name: 'tax', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $tax = 0.0;

    #[ORM\Column(name: 'total', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $total = 0.0;

    #[ORM\Column(name: 'date_created', type: 'datetime', nullable: true)]
    public ?DateTime $dateCreated = null;

    #[ORM\Column(name: 'date_modified', type: 'datetime', nullable: true)]
    public ?DateTime $dateModified = null;

    #[ORM\JoinColumn(name: 'presupuesto', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Presupuesto::class)]
    public Presupuesto $presupuesto;

    #[ORM\JoinColumn(name: 'pedido', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Pedido::class)]
    public Pedido $pedido;

    #[ORM\JoinColumn(name: 'albaran', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Albaran::class)]
    public Albaran $albaran;

    #[ORM\JoinColumn(name: 'albaran_linea', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: AlbaranLinea::class)]
    public AlbaranLinea $albaranLinea;

    #[ORM\JoinColumn(name: 'factura', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Factura::class)]
    public Factura $factura;

    #[ORM\JoinColumn(name: 'factura_linea', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: FacturaLinea::class)]
    public FacturaLinea $facturaLinea;

}