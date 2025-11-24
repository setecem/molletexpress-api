<?php

namespace App\Entity\Profile;

use Cavesman\Db\Doctrine\Entity\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Team
 */
#[ORM\Table(name: 'action_group')]
#[ORM\UniqueConstraint(name: 'name', columns: ['name'])]
#[ORM\Entity]
class ProfileActionGroup extends Entity
{

    #[ORM\Column(name: 'name', type: 'string', unique: true, nullable: true)]
    public ?string $name = null;

    #[ORM\OneToMany(targetEntity: ProfileAction::class, mappedBy: 'group')]
    #[ORM\OrderBy(['id' => 'DESC'])]
    public array|Collection $actions;

    public function __construct()
    {
        $this->actions = new ArrayCollection();
    }

}
