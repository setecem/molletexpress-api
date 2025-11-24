<?php

namespace App\Entity\User;

use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'user_type')]
#[ORM\Entity]
class UserType extends Entity
{

    #[ORM\Column(name: 'reference', type: 'string', length: 50, nullable: true)]
    public ?string $reference = null;

    #[ORM\Column(name: 'name', type: 'string', length: 50, nullable: true)]
    public ?string $name = null;

    #[ORM\Column(name: 'date_created', type: 'datetime', nullable: true)]
    public ?DateTime $dateCreated = null;

    #[ORM\Column(name: 'date_modified', type: 'datetime', nullable: true)]
    public ?DateTime $dateModified = null;

}
