<?php

namespace App\Entity\Document\Factura;


use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'factura_feed')]
#[ORM\Entity]
class FacturaFeed extends Entity
{
    #[ORM\JoinColumn(name: 'factura', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Factura::class)]
    public ?Factura $factura = null;

    #[ORM\OneToOne(targetEntity: FacturaFeedAlert::class, mappedBy: 'feed')]
    public ?FacturaFeedAlert $alert = null;

    #[ORM\Column(name: 'type', type: 'string', nullable: true)]
    public ?string $type = null;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    public ?string $description = null;

    #[ORM\Column(name: 'date', type: 'datetime', nullable: true)]
    public ?DateTime $date = null;

    #[ORM\Column(name: 'private', type: 'boolean', nullable: false, options: ['default' => '0'])]
    public bool $private = false;

}
