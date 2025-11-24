<?php

namespace App\Entity\Document\Pedido;

use App\Entity\Document\DocumentLine;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'pedido_linea')]
#[ORM\Entity]
class PedidoLinea extends DocumentLine
{

}