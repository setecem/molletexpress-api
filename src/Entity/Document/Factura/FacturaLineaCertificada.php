<?php

namespace App\Entity\Document\Factura;

use App\Entity\Document\DocumentLineCertificate;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'factura_linea_certificada')]
#[ORM\Entity]
class FacturaLineaCertificada extends DocumentLineCertificate
{

    #[ORM\JoinColumn(name: 'linea', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: FacturaLinea::class)]
    public FacturaLinea $linea;

}