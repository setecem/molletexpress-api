<?php

namespace App\Model\User;

use App\Model\Client;
use App\Model\Employee\Employee;
use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;
use DateTime;

class User extends Model
{
    const string|Base ENTITY = \App\Entity\User\User::class;
    public ?Employee $employee = null;
    public ?Client $customer = null;
    public ?string $nif = null;
    public ?string $firstname = null;
    public ?string $lastname = null;
    public ?string $ssNum = null;
    public ?string $cccNum = null;
    public ?UserType $type = null;
    public int $contract = 0;
    public int $total_hours = 0;
    public ?string $phone = null;
    public ?string $mobile = null;
    public ?string $email = null;
    public ?string $username = null;
    public ?string $password = null;
    public ?string $token = null;
    public DateTime|string|null $dateStart = null;
    public DateTime|string|null $dateEnd = null;
    public bool $active = true;

    public function typeOfCollection(string $property): ?string
    {
        return match($property) {
            'employee' => Employee::class,
            'customer' => Client::class,
            'type' => UserType::class,
            default => null,
        };
    }
}