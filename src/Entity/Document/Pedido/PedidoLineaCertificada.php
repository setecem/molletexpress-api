<?php

namespace App\Entity\Document\Pedido;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'pedido_linea_certificada')]
#[ORM\Entity]
class PedidoLineaCertificada
{

    #[ORM\JoinColumn(name: 'linea', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: PedidoLinea::class)]
    public PedidoLinea $linea;

}