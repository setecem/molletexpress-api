<?php

namespace App\Entity;

use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'alert')]
#[ORM\Entity]
class Alert extends Entity
{

    #[ORM\Column(name: 'message', type: 'string', length: 400, nullable: true)]
    public ?string $message = null;

    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => true])]
    public bool $active = true;

    #[ORM\Column(name: 'date_alert', type: 'datetime', nullable: true)]
    public ?DateTime $dateAlert = null;

}
