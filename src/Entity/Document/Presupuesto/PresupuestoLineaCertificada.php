<?php

namespace App\Entity\Document\Presupuesto;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'presupuesto_linea_certificada')]
#[ORM\Entity]
class PresupuestoLineaCertificada
{

    #[ORM\JoinColumn(name: 'linea', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: PresupuestoLinea::class)]
    public PresupuestoLinea $linea;

}