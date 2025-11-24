<?php

namespace App\Entity\Document\Albaran;

use App\Entity\Document\DocumentLine;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'albaran_linea')]
#[ORM\Entity]
class AlbaranLinea extends DocumentLine
{

}