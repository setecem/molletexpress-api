<?php

namespace App\Model;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;

class Customer extends Model
{

    const string|Base ENTITY = \App\Entity\Customer::class;
    public ?string $name = null;
    public ?string $nameComercial = null;
    public ?string $address = null;
    public ?string $locale = null;
    public ?string $postcode = null;
    public ?string $province = null;
    public ?string $subscriberNumber = null;
    public ?string $phone = null;
    public ?string $mobile = null;
    public ?string $nif = null;
    public ?string $email = null;
    public ?string $discount = null;
    public ?string $orderNumber = null;
    public ?string $paymentMethod = null;
    public int $paymentTerm = 0;
    public int $paymentDay = 0;
    public ?string $bankingEntity = null;
    public ?string $iban = null;
    public ?bool $active = true;

}