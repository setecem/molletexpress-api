<?php

namespace App\Entity\Document\Albaran;


use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'albaran_feed')]
#[ORM\Entity]
class AlbaranFeed extends Entity
{
    #[ORM\JoinColumn(name: 'albaran', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Albaran::class)]
    public ?Albaran $albaran = null;

    #[ORM\OneToOne(targetEntity: AlbaranFeedAlert::class, mappedBy: 'feed')]
    public ?AlbaranFeedAlert $alert = null;

    #[ORM\Column(name: 'type', type: 'string', nullable: true)]
    public ?string $type = null;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    public ?string $description = null;

    #[ORM\Column(name: 'date', type: 'datetime', nullable: true)]
    public ?DateTime $date = null;

    #[ORM\Column(name: 'private', type: 'boolean', nullable: false, options: ['default' => '0'])]
    public bool $private = false;

}
