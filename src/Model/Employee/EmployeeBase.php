<?php

namespace App\Model\Employee;

use App\Enum\Images;
use App\Model\File;
use App\Model\User\User;
use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;
use DateTime;

class EmployeeBase extends Model
{
    const string|Base ENTITY = \App\Entity\Employee\Employee::class;
    public ?string $name = null;
    public ?string $lastname = null;
    public ?User $user = null;
    public ?string $token = null;
    public array $departments = [];

    public bool $active = true;
    public DateTime|string|null $dateStart = null;
    public DateTime|string|null $dateEnd = null;
    public int $contract = 0;
    public int $hours = 0;

    /** @var File[] $files */
    public array $files = [];

    /** @var EmployeeRole[] $roles  */
    public array $roles = [];

    public Images|string $logo = Images::logoWhiteTextSetecem;

    public Images|string $icono = Images::logoWhiteSetecem;

    public Images|string $fondo = Images::bgSetecem;


    public function typeOfCollection(string $property): ?string
    {
        return match($property) {
            'files' => File::class,
            'roles' => EmployeeRole::class,
            'logos', 'icono', 'fondo' => Images::class,
            default => null
        };
    }

}