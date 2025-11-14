<?php

namespace App\Entity;


use Cavesman\Db\Doctrine\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'client')]
#[ORM\Entity]
class Client extends Entity
{

    #[ORM\Column(name: 'name', type: 'string', nullable: true)]
    public ?string $name = null;

    #[ORM\Column(name: 'name_comercial', type: 'string', nullable: true)]
    public ?string $nameComercial = null;

    #[ORM\Column(name: 'address', type: 'string', nullable: true)]
    public ?string $address = null;

    #[ORM\Column(name: 'locale', type: 'string', nullable: true)]
    public ?string $locale = null;

    #[ORM\Column(name: 'postcode', type: 'string', nullable: true)]
    public ?string $postcode = null;

    #[ORM\Column(name: 'province', type: 'string', nullable: true)]
    public ?string $province = null;

    #[ORM\Column(name: 'phone', type: 'string', nullable: true)]
    public ?string $phone = null;

    #[ORM\Column(name: 'mobile', type: 'string', nullable: true)]
    public ?string $mobile = null;

    #[ORM\Column(name: 'subscriber_number', type: 'integer', nullable: true)]
    public ?string $subscriberNumber = null;

    #[ORM\Column(name: 'nif', type: 'string', nullable: true)]
    public ?string $nif = null;

    #[ORM\Column(name: 'email', type: 'string', nullable: true)]
    public ?string $email = null;

    #[ORM\Column(name: 'discount', type: 'integer', nullable: true)]
    public ?string $discount = null;

    #[ORM\Column(name: 'order_number', type: 'string', nullable: true)]
    public ?string $orderNumber = null;

    #[ORM\Column(name: 'payment_method', type: 'string', nullable: true)]
    public ?string $paymentMethod = null;

    #[ORM\Column(name: 'payment_term', type: 'integer', nullable: false, options: ['default' => '0'])]
    public int $paymentTerm = 0;

    #[ORM\Column(name: 'payment_day', type: 'integer', nullable: false, options: ['default' => '0'])]
    public int $paymentDay = 0;

    #[ORM\Column(name: 'banking_entity', type: 'string', nullable: true)]
    public ?string $bankingEntity = null;

    #[ORM\Column(name: 'iban', type: 'string', nullable: true)]
    public ?string $iban = null;

    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => true])]
    public bool $active = true;

}
