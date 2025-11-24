<?php

namespace App\Model\User;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;


class UserType extends Model
{
    const string|Base ENTITY = \App\Entity\User\UserType::class;
    public ?string $reference = null;
    public ?string $name = null;
}