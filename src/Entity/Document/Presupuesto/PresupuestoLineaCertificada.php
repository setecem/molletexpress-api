<?php

namespace App\Entity\Document\Presupuesto;

use App\Entity\Document\DocumentLineCertificate;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'presupuesto_linea_certificada')]
#[ORM\Entity]
class PresupuestoLineaCertificada extends DocumentLineCertificate
{

    #[ORM\JoinColumn(name: 'linea', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: PresupuestoLinea::class)]
    public PresupuestoLinea $linea;

}