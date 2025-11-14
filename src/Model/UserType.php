<?php

namespace App\Model;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;


class UserType extends Model
{
    const string|Base ENTITY = \App\Entity\UserType::class;
    public ?string $reference = null;
    public ?string $name = null;
}