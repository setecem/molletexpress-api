<?php

namespace App\Model;

use App\Model\Document\Factura\Factura;
use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;
use DateTime;
use Doctrine\Common\Collections\Collection;

class OrdenCobro extends Model
{

    const string|Base ENTITY = \App\Entity\OrdenCobro::class;

    public ?string $reference = null;
    public ?Client $client = null;
    public DateTime|string|null $date = null;
    public ?bool $active = false;
    public bool $pagada = false;
    public array|Collection $facturas = [];


    public function typeOfCollection(string $property): ?string
    {
        return match ($property) {
            'client' => Client::class,
            'facturas' => Factura::class,
            default => null
        };
    }
}