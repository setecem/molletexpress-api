<?php

namespace App\Model\Verifactu;

class FacturaVerifactu
{
    public ?string $serie = null;
    public ?string $numero = null;
    public ?string $fecha_expedicion = null;
    public ?string $rechazo_previo = null;
    public ?string $tipo_factura = null;
    public ?string $descripcion = null;
    public ?string $nif = null;
    public ?string $nombre = null;
    public ?string $tipo_rectificativa = null;

    /** @var Linea[] */
    public array $lineas = [];
    public ?string $importe_total = null;
    public ?IdOtro $id_otro = null;
    public ?string $validar_destinatario = null;
    public ?ImporteRectificativa $importe_rectificativa = null;
    public ?string $incidencia = null;
    public ?Especial $especial = null;
}
