<?php

namespace App\Model;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;

class Client extends Model
{

    const string|Base ENTITY = \App\Entity\Client::class;
    public ?string $name = null;
    public ?string $nameComercial = null;
    public ?string $direccion = null;
    public ?string $localidad = null;
    public ?string $codigoPostal = null;
    public ?string $provincia = null;
    public ?string $numAbonado = null;
    public ?string $telefono = null;
    public ?string $movil = null;
    public ?string $nif = null;
    public ?string $email = null;
    public ?string $descuento = null;
    public ?string $numPedido = null;
    public ?string $formaPago = null;
    public int $diasPago = 0;
    public int $diaFijoPago = 0;
    public ?string $banco = null;
    public ?string $iban = null;
    public ?bool $active = true;
    public int $debe = 0;


}