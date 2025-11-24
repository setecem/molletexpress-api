<?php

namespace App\Entity\Document\Factura;


use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'factura_feed_alert')]
#[ORM\Entity]
class FacturaFeedAlert extends Entity
{

    #[ORM\JoinColumn(name: 'feed', referencedColumnName: 'id')]
    #[ORM\OneToOne(targetEntity: FacturaFeed::class)]
    public ?FacturaFeed $feed = null;

    #[ORM\Column(name: 'active', type: 'boolean', options: ['default' => true])]
    public bool $active = true;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    public ?string $description = null;

    #[ORM\Column(name: 'date', type: 'datetime', nullable: true, options: ['default' => null])]
    public ?DateTime $date = null;

}
