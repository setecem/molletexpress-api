<?php

namespace App\Entity\Contact;

use Cavesman\Db\Doctrine\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'contact_file')]
#[ORM\Entity]
class ContactFile extends Entity
{

    #[ORM\Column(name: 'extension', type: 'string', nullable: true)]
    public ?string $extension = null;

    #[ORM\Column(name: 'name', type: 'string', nullable: true)]
    public ?string $name = null;

    #[ORM\JoinColumn(name: 'contact', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Contact::class)]
    public ?Contact $contact = null;

    #[ORM\Column(name: 'size', type: 'integer', nullable: false, options: ['default' => 0])]
    public int $size = 0;

    #[ORM\Column(name: 'type', type: 'string', nullable: true)]
    public ?string $type = null;

    #[ORM\Column(name: 'mime_type', type: 'string', nullable: true)]
    public ?string $mimeType = null;

    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => true])]
    public bool $active = true;

    #[ORM\Column(name: 'private', type: 'boolean', nullable: false, options: ['default' => '0'])]
    public bool $private = false;

}
