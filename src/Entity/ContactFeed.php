<?php

namespace App\Entity;


use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'contact_feed')]
#[ORM\Entity]
class ContactFeed extends Entity
{
    #[ORM\JoinColumn(name: 'contact', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Contact::class)]
    public ?Contact $contact = null;

    #[ORM\OneToOne(targetEntity: ContactFeedAlert::class, mappedBy: 'feed')]
    public ?ContactFeedAlert $alert = null;

    #[ORM\Column(name: 'type', type: 'string', nullable: true)]
    public ?string $type = null;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    public ?string $description = null;

    #[ORM\Column(name: 'date', type: 'datetime', nullable: true)]
    public ?DateTime $date = null;

    #[ORM\Column(name: 'private', type: 'boolean', nullable: false, options: ['default' => '0'])]
    public bool $private = false;

}
