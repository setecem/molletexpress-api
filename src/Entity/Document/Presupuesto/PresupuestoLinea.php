<?php

namespace App\Entity\Document\Presupuesto;

use App\Entity\Document\DocumentLine;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'presupuesto_linea')]
#[ORM\Entity]
class PresupuestoLinea extends DocumentLine
{

}