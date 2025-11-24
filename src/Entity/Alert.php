<?php

namespace App\Entity;

use App\Entity\User\User;
use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'alert')]
#[ORM\Entity]
class Alert extends Entity
{
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    public User $user;

    #[ORM\Column(name: 'message', type: 'string', length: 400, nullable: true)]
    public ?string $message = null;

    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => true])]
    public bool $active = true;

    #[ORM\Column(name: 'date_alert', type: 'datetime', nullable: true)]
    public ?DateTime $dateAlert = null;

    #[ORM\Column(name: 'date_created', type: 'datetime', nullable: true)]
    public ?DateTime $dateCreated = null;

    #[ORM\Column(name: 'date_modified', type: 'datetime', nullable: true)]
    public ?DateTime $dateModified = null;

}
