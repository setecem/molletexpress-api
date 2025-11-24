<?php

namespace App\Entity\User;

use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'user_hours_history')]
#[ORM\Entity]
class UserHoursHistory extends Entity
{

    #[ORM\JoinColumn(name: 'hour_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: UserHours::class, cascade: ['persist'], inversedBy: 'history')]
    public UserHours $hour;

    #[ORM\Column(name: 'date_start', type: 'datetime', nullable: true)]
    public ?DateTime $dateStart = null;

    #[ORM\Column(name: 'date_end', type: 'datetime', nullable: true)]
    public ?DateTime $dateEnd = null;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'users')]
    public User $user;

    #[ORM\Column(name: 'issue', type: 'boolean', nullable: false, options: ['default' => false])]
    public bool $issue = false;

    #[ORM\Column(name: 'motivo', type: 'string', nullable: true)]
    public ?string $motivo = null;

}
