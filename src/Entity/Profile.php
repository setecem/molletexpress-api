<?php

namespace App\Entity;

use Cavesman\Db\Doctrine\Entity\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Table(name: 'profile')]
#[ORM\Entity]
class Profile extends Entity
{

    #[ORM\OneToMany(targetEntity: Profile::class, mappedBy: 'parent')]
    public array|Collection $children;

    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Profile::class, inversedBy: 'children')]
    public Profile $parent;

    #[ORM\Column(name: 'name', type: 'string', nullable: true)]
    public ?string $name = null;

    #[ORM\Column(name: 'reference', type: 'string', nullable: true)]
    public ?string $reference = null;

    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => true])]
    public bool $active = true;

    #[ORM\JoinTable(name: 'profile_user')]
    #[ORM\JoinColumn(name: 'profile_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'profiles')]
    public array|Collection $users;

    #[ORM\JoinTable(name: 'profile_employee')]
    #[ORM\JoinColumn(name: 'profile_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'employee_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Employee::class, inversedBy: 'profiles')]
    public array|Collection $employees;

    #[ORM\JoinTable(name: 'profile_action')]
    #[ORM\JoinColumn(name: 'action_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'profile_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: ProfileAction::class, inversedBy: 'profiles')]
    public array|Collection $actions;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->employees = new ArrayCollection();
        $this->actions = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

}
