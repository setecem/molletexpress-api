<?php

namespace App\Model\Document;

class DocumentoLinea {
    public string $reference;
    public string $unidadMedida;
    public string $description;
    public float $quantity = 0;
    public float $discount = 0;
    public float $quantityCertificada = 0;
    public float $price = 0;
    public float $tax = 0;
    public float $total = 0;
}