<?php

namespace App\Entity;


use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'employee_movement')]
#[ORM\Entity]
class EmployeeMovement extends Entity
{

    #[ORM\JoinColumn(name: 'employee', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Employee::class)]
    public ?Employee $employee = null;

    #[ORM\Column(name: 'type', type: 'string', nullable: true)]
    public ?string $type = null;

    #[ORM\Column(name: 'amount', type: 'float', nullable: false, options: ['default' => '0'])]
    public float $amount = 0;

    #[ORM\Column(name: 'date', type: 'date', nullable: true, options: ['default' => null])]
    public ?DateTime $date = null;

    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => true])]
    public bool $active = true;
}
