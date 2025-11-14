<?php

namespace App\Entity;

use Cavesman\Db\Doctrine\Entity\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'action')]
#[ORM\Entity]
class ProfileAction extends Entity
{

    #[ORM\Column(name: 'name', type: 'string', nullable: true)]
    public ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Profile::class, mappedBy: 'users')]
    public array|Collection $profiles;

    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: ProfileActionGroup::class, inversedBy: 'actions')]
    public ProfileActionGroup $group;

    public function __construct()
    {
        $this->profiles = new ArrayCollection();
    }
}
