<?php

namespace App\Entity\Document\Factura;

use App\Entity\Document\Documento;
use App\Entity\OrdenCobro;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'factura')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Factura extends Documento
{

    /** @var FacturaLinea[]|Collection */
    #[ORM\OneToMany(targetEntity: FacturaLinea::class, mappedBy: 'factura', cascade: ['persist'])]
    public array|Collection $lineas = [];

    #[ORM\JoinColumn(name: 'orden_cobro', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: OrdenCobro::class)]
    public ?OrdenCobro $ordenCobro = null;

    #[ORM\PostPersist]
    public function generateNumber(): void
    {

        if ($this->number !== null)
            return;

        $this->number = 'F' . sprintf('%03d-%03d', intdiv($this->id, 1000), $this->id % 1000);
    }
}
