<?php

namespace App\Model\Employee;

use App\Enum\Images;
use App\Model\File;
use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;
use Doctrine\Common\Collections\Collection;

class EmployeeBase extends Model
{
    const string|Base ENTITY = \App\Entity\Employee\Employee::class;

    public ?string $name = null;
    public ?string $lastname = null;
    public ?string $username = null;
    public ?string $email = null;
    public bool $active = true;
    public ?string $dni = null;
    public float $salarioBase = 0;
    public float $salarioMensualNominal = 0;
    public float $plusExtra = 0;
    public float $precioHora = 0;
    public float $precioHoraExtra = 0;
    public float $precioHoraFestivo = 0;

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
            'roles' => EmployeeRole::class,
            'files' => File::class,
            'logos', 'icono', 'fondo' => Images::class,
            default => null
        };
    }

}