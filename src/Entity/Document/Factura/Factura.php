<?php

namespace App\Entity\Document\Factura;

use App\Entity\Document\Albaran\Albaran;
use App\Entity\Document\Documento;
use App\Entity\OrdenCobro;
use Cavesman\Config;
use Cavesman\Db;
use Cavesman\Exception\ModuleException;
use DateInterval;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Table(name: 'factura')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Factura extends Documento
{
    #[ORM\JoinColumn(name: 'albaran', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Albaran::class, inversedBy: 'albaranes')]
    public ?Albaran $albaran = null;

    /** @var FacturaLinea[]|Collection */
    #[ORM\OneToMany(targetEntity: FacturaLinea::class, mappedBy: 'factura', cascade: ['persist'])]
    public array|Collection $lineas = [];

    #[ORM\JoinColumn(name: 'orden_cobro', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: OrdenCobro::class)]
    public ?OrdenCobro $ordenCobro = null;

}
