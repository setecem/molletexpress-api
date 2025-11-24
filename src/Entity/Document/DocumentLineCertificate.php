<?php

namespace App\Entity\Document;

use App\Entity\Document\Albaran\Albaran;
use App\Entity\Document\Factura\Factura;
use App\Entity\Document\Pedido\Pedido;
use App\Entity\Document\Presupuesto\Presupuesto;
use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

abstract class DocumentLineCertificate extends Entity
{

    #[ORM\Column(name: 'quantity', type: 'decimal', precision: 12, scale: 2, nullable: false, options: ['default' => 0.0])]
    public float $quantity = 0.0;

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

    #[ORM\JoinColumn(name: 'factura', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Factura::class)]
    public Factura $factura;


}