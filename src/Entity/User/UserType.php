<?php

namespace App\Entity\User;

use Cavesman\Db\Doctrine\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'user_type')]
#[ORM\Entity]
class UserType extends Entity
{

    #[ORM\Column(name: 'reference', type: 'string', nullable: true)]
    public ?string $reference = null;

    #[ORM\Column(name: 'name', type: 'string', nullable: true)]
    public ?string $name = null;

}
