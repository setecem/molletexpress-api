<?php

namespace App\Entity\User;

use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'user_hours')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class UserHours extends Entity
{

    #[ORM\Column(name: 'date_start', type: 'datetime', nullable: true)]
    public ?DateTime $dateStart = null;

    #[ORM\Column(name: 'date_end', type: 'datetime', nullable: true)]
    public ?DateTime $dateEnd = null;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'users')]
    public User $user;

    /** TODO Estaban en crm, ¿Mantener aquí? */
    #[ORM\Column(name: 'comment', type: 'text', nullable: true)]
    public ?string $comment = null;

    #[ORM\OneToMany(targetEntity: UserHoursHistory::class, mappedBy: 'hour')]
    #[ORM\OrderBy(['id' => 'DESC'])]
    public array|Collection $history;

}
