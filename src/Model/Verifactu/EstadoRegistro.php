<?php

namespace App\Model\Verifactu;

class EstadoRegistro
{
    public ?string $nif = null;
    public ?string $serie = null;
    public ?string $numero = null;
    public ?string $fecha_expedicion = null;
    public ?string $operacion = null;
    public ?string $estado = null;
    public ?string $url = null;
    public ?string $qr = null;
    public ?string $codigo_error = null;
    public ?string $mensaje_error = null;
    public ?string $estado_registro_duplicado = null;
}