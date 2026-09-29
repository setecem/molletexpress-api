<?php

namespace App\Entity\Document\Albaran;

use App\Entity\Document\Documento;
use App\Entity\Document\Factura\Factura;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'albaran')]
#[ORM\Entity]
class Albaran extends Documento
{
    #[ORM\JoinColumn(name: 'factura', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Factura::class, inversedBy: 'facturas')]
    public ?Factura $factura = null;

    /** @var AlbaranLinea[]|Collection */
    #[ORM\OneToMany(targetEntity: AlbaranLinea::class, mappedBy: 'albaran', cascade: ['persist'])]
    public array|Collection $lineas = [];

    #[ORM\Column(name: 'email_sent', type: 'boolean', nullable: false, options: ['default' => '0'])]
    public bool $emailSent = false;

    /** Papel en el DeCA: TRANSPORTISTA_EFECTIVO o CARGADOR_CONTRACTUAL (ver Model\Albaran::TRANSPORT_ROLES). */
    #[ORM\Column(name: 'transport_role', type: 'string', length: 32, nullable: true)]
    public ?string $transportRole = null;

    /** Id del borrador en DeCA (creado a través de su API pública al dar de alta el albarán desde appDeCA). */
    #[ORM\Column(name: 'deca_id', type: 'integer', nullable: true)]
    public ?int $decaId = null;

    /** CREATED si el borrador existe en DeCA; ERROR si falló (ver decaError y reintentar). */
    #[ORM\Column(name: 'deca_status', type: 'string', length: 16, nullable: true)]
    public ?string $decaStatus = null;

    #[ORM\Column(name: 'deca_error', type: 'text', nullable: true)]
    public ?string $decaError = null;

}
