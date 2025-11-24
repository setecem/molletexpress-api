<?php

namespace App\Entity\Document\Pedido;

use App\Entity\Document\DocumentLineCertificate;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'pedido_linea_certificada')]
#[ORM\Entity]
class PedidoLineaCertificada extends DocumentLineCertificate
{

    #[ORM\JoinColumn(name: 'linea', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: PedidoLinea::class)]
    public PedidoLinea $linea;

}