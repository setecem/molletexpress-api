## Document Entity

### OrdenCobro (en Albaran)

    /**
     * @ORM\ManyToOne(targetEntity="\src\Modules\OrdenCobro\Entity\OrdenCobroEntity")
     * @ORM\JoinColumn(name="orden", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $orden;

### Marcas de tiempo: 
Ahora son createdOn, updatedOn

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    protected $dateCreated = NULL;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modified", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    protected $dateModified = NULL;

### Presupuesto: 
No hay ninguna entidad Presupuesto por lo que tampoco hay en documento una relación con esta clase

    /**
     * @ORM\ManyToOne(targetEntity="\src\Modules\Factura\Entity\PresupuestoEntity", inversedBy="presupuestos")
     * @ORM\JoinColumn(name="presupuesto", referencedColumnName="id")
     */
    protected $presupuesto;

### Pedido: 
No hay ninguna entidad Pedido por lo que tampoco hay en documento una relación con esta clase

    /**
     * @ORM\ManyToOne(targetEntity="\src\Modules\Factura\Entity\PedidoEntity" , inversedBy="pedidos")
     * @ORM\JoinColumn(name="pedido", referencedColumnName="id")
     */
    protected $pedido;

### Albaran (en Albaran):
Albaran debería tener referencia a Albaran?¿

    /**
     * @ORM\ManyToOne(targetEntity="\src\Modules\Factura\Entity\AlbaranEntity", inversedBy="albaranes")
     * @ORM\JoinColumn(name="albaran", referencedColumnName="id")
     */
    protected $albaran;

### Factura (en Factura):
Factura debería tener referencia a Factura?¿

    /**
     * @ORM\ManyToOne(targetEntity="\src\Modules\Factura\Entity\FacturaEntity", inversedBy="facturas")
     * @ORM\JoinColumn(name="factura", referencedColumnName="id")
     */
    protected $factura;

## DocumentLine Entity

### Marcas de tiempo:
Ahora son createdOn, updatedOn

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    protected $dateCreated = NULL;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modified", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    protected $dateModified = NULL;

### Presupuesto:
No hay ninguna entidad Presupuesto por lo que tampoco hay en la línea de documento una relación con esta clase

    /**
     * @ORM\ManyToOne(targetEntity="\src\Modules\Factura\Entity\PresupuestoEntity")
     * @ORM\JoinColumn(name="presupuesto", referencedColumnName="id")
     */
    protected $presupuesto;

### Pedido:
No hay ninguna entidad Pedido por lo que tampoco hay en la línea de documento una relación con esta clase

    /**
     * @ORM\ManyToOne(targetEntity="\src\Modules\Factura\Entity\PedidoEntity")
     * @ORM\JoinColumn(name="pedido", referencedColumnName="id")
     */
    protected $pedido;

### AlbaranLinea (en AlbaranLinea):
AlbaranLinea debería tener referencia a AlbaranLinea?¿

    /**
     * @ORM\ManyToOne(targetEntity="\src\Modules\Factura\Entity\AlbaranLineaEntity")
     * @ORM\JoinColumn(name="albaran_linea", referencedColumnName="id")
     */
    protected $albaran_linea;

### FacturaLinea (en FacturaLinea):
FacturaLinea debería tener referencia a FacturaLinea?¿

    /**
     * @ORM\ManyToOne(targetEntity="\src\Modules\Factura\Entity\FacturaLineaEntity")
     * @ORM\JoinColumn(name="factura_linea", referencedColumnName="id")
     */
    protected $factura_linea;

## Employee Entity

### User:
No hay user asignado

    /**
     * @var \src\Modules\User\Entity\UserEntity
     *
     * @ORM\ManyToOne(targetEntity="\src\Modules\User\Entity\UserEntity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user = NULL;

### CosteFijo

    /**
     * @var decimal
     *
     * @ORM\Column(name="coste_fijo", type="decimal", precision=10, scale=2, nullable=true, options={"default": 0})
     */
    private $coste_fijo = 0;

### Despido30Dias

    /**
     * @var decimal
     *
     * @ORM\Column(name="despido_30_dias", type="decimal", precision=10, scale=2, nullable=false, options={"default": 0})
     */
    private $despido_30_dias = 0;

### PlusGuardias

    /**
     * @var decimal
     *
     * @ORM\Column(name="plus_guardias", type="decimal", precision=10, scale=2, nullable=false, options={"default": 0})
     */
    private $plus_guardias = 0;

### Adelantos

    /**
     * @var decimal
     *
     * @ORM\Column(name="adelantos", type="decimal", precision=10, scale=2, nullable=false, options={"default": 0})
     */
    private $adelantos = 0;

### Retenciones

    /**
     * @var decimal
     *
     * @ORM\Column(name="retenciones", type="decimal", precision=10, scale=2, nullable=false, options={"default": 0})
     */
    private $retenciones = 0;

## Client Entity

### Marcas de tiempo:
Ahora son createdOn, updatedOn

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dateCreated = NULL;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modified", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dateModified = NULL;

## OrdenCobro Entity

### Albaranes

    /**
     * One product has many features. This is the inverse side.
     * @ORM\OneToMany(targetEntity="\src\Modules\Factura\Entity\AlbaranEntity", mappedBy="orden")
     */
    private $albaranes;

## Entidades
No hay Pedido, PedidoLinea, Presupuesto, PresupuestoLinea