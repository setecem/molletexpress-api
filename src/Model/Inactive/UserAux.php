<?php


namespace App\Model\Inactive;


use App\Model\UserType;
use BackedEnum;
use Cavesman\Db\Doctrine\Model\Base;

/**
 * Model for user update and response
 * User (NO USAR SALVO QUE NECESITES)
 */
class UserAux extends Base
{
    const string|\Cavesman\Db\Doctrine\Entity\Base ENTITY = \App\EntityAux\User::class;

    public ?int $id = null;
    public ?string $firstname = null;
    public ?string $lastname = null;
    public ?string $email = null;
    public ?UserType $type = null;
    public ?Enterprise $enterprise = null;
    public int $number = 0;
    public ?string $role = null;
    public ?string $profile = null;
    public ?string $dni = null;
    public ?string $mobile = null;

    public array $buildings = [];
    public bool $active;

    public function typeOfCollection(string $property): ?string
    {
        return match ($property) {
            'enterprise' => Enterprise::class,
            default => null,
        };
    }

    public function typeOfEnum($name): BackedEnum|string|null
    {
        return match ($name) {
            'type' => UserType::class,
            default => null
        };
    }
}
