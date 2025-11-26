<?php

namespace App\Model\Document;

use App\Enum\DocumentStatus;
use App\Model\Client;
use Cavesman\Db\Doctrine\Model\Model;
use DateTime;

abstract class Documento extends Model
{
    public DocumentStatus $status = DocumentStatus::DRAFT;

    public ?\App\Model\Client $client = null;
    public string $serie = 'P';
    public ?int $reference = null;
    public ?int $numPedido = 0;
    public ?string $number = null;
    public DateTime $date;
    public ?string $observaciones = null;

    public float $importeBruto = 0;
    public float $subtotal = 0;
    public string $tax = '21';
    public float $total = 0;
    public float $pagado = 0;
    public float $balance = 0;
    public float $discount = 0;
    public float $discountPP = 0;
    public float $impDiscount = 0;
    public float $impDiscountPP = 0;
    public ?string $comments = null;
    public bool $pagada = false;
    public bool $bloqueada = true;
    public array $lineas = [];


    public function typeOfCollection(string $property): ?string
    {
        return match ($property) {
            'client' => Client::class,
            'lineas' => DocumentoLinea::class,
            default => null
        };
    }
}
