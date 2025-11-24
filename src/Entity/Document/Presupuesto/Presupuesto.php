<?php

namespace App\Entity\Document\Presupuesto;

use App\Entity\Document\Document;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'presupuesto')]
#[ORM\Entity]
class Presupuesto extends Document
{

}