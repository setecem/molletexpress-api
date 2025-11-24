<?php

namespace App\Entity\Document\Albaran;

use App\Entity\Document\DocumentLineCertificate;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'albaran_linea_certificada')]
#[ORM\Entity]
class AlbaranLineaCertificada extends DocumentLineCertificate
{

    #[ORM\JoinColumn(name: 'linea', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: AlbaranLinea::class)]
    public AlbaranLinea $linea;

}