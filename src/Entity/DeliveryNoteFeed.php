<?php

namespace App\Entity;


use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'delivery_note_feed')]
#[ORM\Entity]
class DeliveryNoteFeed extends Entity
{
    #[ORM\JoinColumn(name: 'delivery_note', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: DeliveryNote::class)]
    public ?DeliveryNote $delivery_note = null;

    #[ORM\OneToOne(targetEntity: DeliveryNoteFeedAlert::class, mappedBy: 'feed')]
    public ?DeliveryNoteFeedAlert $alert = null;

    #[ORM\Column(name: 'type', type: 'string', nullable: true)]
    public ?string $type = null;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    public ?string $description = null;

    #[ORM\Column(name: 'date', type: 'datetime', nullable: true)]
    public ?DateTime $date = null;

    #[ORM\Column(name: 'private', type: 'boolean', nullable: false, options: ['default' => '0'])]
    public bool $private = false;

}
