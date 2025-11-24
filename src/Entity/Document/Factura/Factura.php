<?php

namespace App\Entity\Document\Factura;

use App\Entity\Document\Document;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'factura')]
#[ORM\Entity]
class Factura extends Document
{

}
