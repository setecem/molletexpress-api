<?php

namespace App\Entity\Document\Albaran;


use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'albaran_feed_alert')]
#[ORM\Entity]
class AlbaranFeedAlert extends Entity
{

    #[ORM\JoinColumn(name: 'feed', referencedColumnName: 'id')]
    #[ORM\OneToOne(targetEntity: AlbaranFeed::class)]
    public ?AlbaranFeed $feed = null;

    #[ORM\Column(name: 'active', type: 'boolean', options: ['default' => true])]
    public bool $active = true;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    public ?string $description = null;

    #[ORM\Column(name: 'date', type: 'datetime', nullable: true, options: ['default' => null])]
    public ?DateTime $date = null;

}
