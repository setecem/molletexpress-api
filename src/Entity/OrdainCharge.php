<?php

namespace App\Entity;

use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'ordain_charge')]
#[ORM\Entity]
class OrdainCharge extends Entity
{

    #[ORM\Column(name: 'reference', type: 'string', nullable: true)]
    public ?string $ref = null;

    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Client::class, cascade: ['persist'])]
    public ?Client $customer = null;

    #[ORM\Column(name: 'amount', type: 'integer', nullable: false, options: ['default' => '0'])]
    public int $amount = 0;

    #[ORM\Column(name: 'date', type: 'datetime', nullable: true)]
    public ?DateTime $date = null;

}
