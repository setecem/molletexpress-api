<?php

namespace App\Model\Verifactu;

class Especial
{
    public ?string $factura_simplificada_art_7273 = null;
    public ?string $emitida_por_tercero_o_destinatario = null;
    public ?string $nombre_tercero = null;
    public ?string $nif_tercero = null;
    public ?IdOtroTercero $id_otro_tercero = null;
}
