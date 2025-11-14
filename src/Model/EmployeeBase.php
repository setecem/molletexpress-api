<?php

namespace App\Model;

use App\Enum\Images;
use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;
use DateTime;

class EmployeeBase extends Model
{
    const string|Base ENTITY = \App\Entity\Employee::class;
    public ?string $name = null;
    public ?string $lastname = null;
    public ?User $user = null;
    public ?string $username = null;
    public ?string $token = null;
    public ?EmployeeBase $comercial = null;
    public ?string $origen = null;
    public array $profiles = [];
    public array $departments = [];
    public ?string $dni = null;
    public ?string $ssNum = null;
    public ?string $cccNum = null;
    public ?string $phone = null;
    public ?string $mobile = null;
    public ?string $email = null;
    public bool $active = true;
    public bool $needComment = false;
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
            'comercial' => EmployeeBase::class,
            'files' => File::class,
            'roles' => EmployeeRole::class,
            'logos', 'icono', 'fondo' => Images::class,
            default => null
        };
    }

}