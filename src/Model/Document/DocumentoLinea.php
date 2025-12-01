<?php

namespace App\Model\Document;

use Cavesman\Db\Doctrine\Model\Model;

class DocumentoLinea extends Model {
    public ?string $reference = null;
    public ?string $unidadMedida = null;
    public ?string $description = null;
    public float $quantity = 0;
    public float $discount = 0;
    public float $quantityCertificada = 0;
    public float $price = 0;
    public float $tax = 0;
    public float $total = 0;
}