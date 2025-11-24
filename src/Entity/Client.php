<?php

namespace App\Entity;


use App\Entity\User\User;
use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'client')]
#[ORM\Entity]
class Client extends Entity
{

    #[ORM\Column(name: 'name', type: 'string', nullable: false)]
    public string $name;

    #[ORM\Column(name: 'name_comercial', type: 'string', nullable: false)]
    public string $nameComercial;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    public ?User $user = null;

    #[ORM\Column(name: 'nif', type: 'string', nullable: false)]
    public string $nif;

    #[ORM\Column(name: 'email', type: 'string', nullable: false)]
    public ?string $email = null;

    #[ORM\Column(name: 'direccion', type: 'string', nullable: false)]
    public string $address;

    #[ORM\Column(name: 'localidad', type: 'string', nullable: false)]
    public string $locale;

    #[ORM\Column(name: 'postal_code', type: 'string', nullable: false)]
    public string $postcode;

    #[ORM\Column(name: 'provincia', type: 'string', nullable: false)]
    public string $province;

    #[ORM\Column(name: 'telefono', type: 'string', nullable: false)]
    public string $phone;

    #[ORM\Column(name: 'movil', type: 'string', nullable: false)]
    public string $mobile;

    #[ORM\Column(name: 'forma_pago', type: 'string', nullable: false)]
    public string $paymentMethod;

    #[ORM\Column(name: 'dias_pago', type: 'integer', nullable: false, options: ['default' => '0'])]
    public int $paymentTerm = 0;

    #[ORM\Column(name: 'dia_fijo_pago', type: 'integer', nullable: false, options: ['default' => '0'])]
    public int $paymentDay = 0;

    #[ORM\Column(name: 'banco', type: 'string', nullable: false)]
    public string $bankingEntity;

    #[ORM\Column(name: 'iban', type: 'string', nullable: false)]
    public string $iban;

    #[ORM\Column(name: 'num_abonado', type: 'integer', nullable: false)]
    public string $subscriberNumber;

    #[ORM\Column(name: 'descuento', type: 'integer', nullable: true)]
    public ?int $discount = null;

    #[ORM\Column(name: 'num_pedido', type: 'string', nullable: true)]
    public ?string $orderNumber = null;

    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => true])]
    public bool $active = true;

    #[ORM\Column(name: 'date_created', type: 'datetime', nullable: true)]
    public ?DateTime $dateCreated = null;

    #[ORM\Column(name: 'date_modified', type: 'datetime', nullable: true)]
    public ?DateTime $dateModified = null;

    public int $debe = 0;
}
