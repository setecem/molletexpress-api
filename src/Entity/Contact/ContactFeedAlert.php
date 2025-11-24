<?php

namespace App\Entity\Contact;


use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'contact_feed_alert')]
#[ORM\Entity]
class ContactFeedAlert extends Entity
{

    #[ORM\JoinColumn(name: 'feed', referencedColumnName: 'id')]
    #[ORM\OneToOne(targetEntity: ContactFeed::class)]
    public ?ContactFeed $feed = null;

    #[ORM\Column(name: 'active', type: 'boolean', options: ['default' => true])]
    public bool $active = true;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    public ?string $description = null;

    #[ORM\Column(name: 'date', type: 'datetime', nullable: true, options: ['default' => null])]
    public ?DateTime $date = null;

}
