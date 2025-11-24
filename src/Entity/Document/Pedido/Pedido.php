<?php

namespace App\Entity\Document\Pedido;

use App\Entity\Document\Document;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'pedido')]
#[ORM\Entity]
class Pedido extends Document
{


}