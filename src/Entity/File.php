<?php

namespace App\Entity;

use App\Entity\Employee\Employee;
use App\Enum\FileType;
use Cavesman\Db\Doctrine\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'file')]
#[ORM\Entity]
class File extends Entity
{
    #[ORM\Column(name: 'name', type: 'string', nullable: true)]
    public ?string $name = null;

    #[ORM\Column(name: 'size', type: 'integer', nullable: false, options: ['default' => 0])]
    public int $size = 0;

    #[ORM\Column(name: "mime", type: "string", nullable: true)]
    public ?string $mime = null;

    #[ORM\JoinColumn(name: 'employee', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Employee::class)]
    public ?Employee $employee = null;

    #[ORM\Column(name: "type", type: "enum", enumType: FileType::class)]
    public FileType $type = FileType::GENERAL;

    #[ORM\Column(name: 'private', type: 'boolean', nullable: false, options: ['default' => '0'])]
    public bool $private = false;

}