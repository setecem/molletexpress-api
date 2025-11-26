<?php

namespace App\Model\Verifactu;

class FacturaVerifactuRegistro
{
    public ?string $uuid = null;
    public ?string $nif_emisor = null;
    public ?string $num_serie = null;
    public ?string $fecha_expedicion = null;
    public ?string $subsanacion = null;
    public ?string $rechazo_previo = null;
    public ?string $tipo_factura = null;
    public ?string $descripcion = null;
    public ?string $nif_destinatario = null;
    public ?string $nombre_destinatario = null;

    /** @var LineaFacturaRegistro[] */
    public array $lineas = [];
    public ?string $importe_total = null;
    public ?string $cuota_total = null;
    public ?Encadenamiento $encadenamiento = null;
    public ?string $huella = null;
    public ?string $ultima_modificacion = null;
    public ?string $estado = null;
}