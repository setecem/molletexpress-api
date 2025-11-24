<?php

namespace App\Entity\Document\Factura;

use App\Entity\Document\DocumentLine;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'factura_linea')]
#[ORM\Entity]
class FacturaLinea extends DocumentLine
{

}