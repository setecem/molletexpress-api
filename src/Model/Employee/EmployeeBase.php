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
    public ?string $username = null;
    public ?string $email = null;
    public bool $active = true;

    /** @var EmployeeRole[] $roles  */
    public array $roles = [];

    public Images|string $logo = Images::logoWhiteTextSetecem;

    public Images|string $icono = Images::logoWhiteSetecem;

    public Images|string $fondo = Images::bgSetecem;


    public function typeOfCollection(string $property): ?string
    {
        return match($property) {
            'roles' => EmployeeRole::class,
            'logos', 'icono', 'fondo' => Images::class,
            default => null
        };
    }

}