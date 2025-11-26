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

    #[ORM\Column(name: 'nif', type: 'string', nullable: true)]
    public ?string $nif = null;

    #[ORM\Column(name: 'email', type: 'string', nullable: true)]
    public ?string $email = null;

    #[ORM\Column(name: 'direccion', type: 'string', nullable: true)]
    public ?string $direccion = null;

    #[ORM\Column(name: 'localidad', type: 'string', nullable: true)]
    public ?string $localidad = null;

    #[ORM\Column(name: 'postal_code', type: 'string', nullable: true)]
    public ?string $codigo_postal = null;

    #[ORM\Column(name: 'provincia', type: 'string', nullable: true)]
    public ?string $provincia = null;

    #[ORM\Column(name: 'telefono', type: 'string', nullable: true)]
    public ?string $telefono = null;

    #[ORM\Column(name: 'movil', type: 'string', nullable: true)]
    public ?string $movil = null;

    #[ORM\Column(name: 'forma_pago', type: 'string', nullable: true)]
    public ?string $formaPago = null;

    #[ORM\Column(name: 'dias_pago', type: 'integer', nullable: false, options: ['default' => '0'])]
    public int $diasPago = 0;

    #[ORM\Column(name: 'dia_fijo_pago', type: 'integer', nullable: false, options: ['default' => '0'])]
    public int $diaFijoPago = 0;

    #[ORM\Column(name: 'banco', type: 'string', nullable: true)]
    public ?string $banco = null;

    #[ORM\Column(name: 'iban', type: 'string', nullable: true)]
    public ?string $iban = null;

    #[ORM\Column(name: 'num_abonado', type: 'integer', nullable: true)]
    public ?string $numAbonado = null;

    #[ORM\Column(name: 'descuento', type: 'integer', nullable: true)]
    public ?int $descuento = null;

    #[ORM\Column(name: 'num_pedido', type: 'string', nullable: true)]
    public ?string $numPedido = null;

    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => true])]
    public bool $active = true;

    public int $debe = 0;
}
