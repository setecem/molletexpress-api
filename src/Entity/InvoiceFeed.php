<?php

namespace App\Entity;


use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'invoice_feed')]
#[ORM\Entity]
class InvoiceFeed extends Entity
{
    #[ORM\JoinColumn(name: 'invoice', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Invoice::class)]
    public ?Invoice $invoice = null;

    #[ORM\OneToOne(targetEntity: InvoiceFeedAlert::class, mappedBy: 'feed')]
    public ?InvoiceFeedAlert $alert = null;

    #[ORM\Column(name: 'type', type: 'string', nullable: true)]
    public ?string $type = null;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    public ?string $description = null;

    #[ORM\Column(name: 'date', type: 'datetime', nullable: true)]
    public ?DateTime $date = null;

    #[ORM\Column(name: 'private', type: 'boolean', nullable: false, options: ['default' => '0'])]
    public bool $private = false;

}
