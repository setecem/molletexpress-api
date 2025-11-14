<?php

namespace App\Model;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;

class InvoiceFile extends Model
{

    const string|Base ENTITY = \App\Entity\InvoiceFile::class;
    public ?string$extension = null;
    public ?string $name = null;
    public ?Invoice $invoice = null;
    public int $size = 0;
    public ?string $type = null;
    public ?string $mimetype = null;
    public bool $active = true;
    public bool $private = true;

    public function typeOfCollection(string $property): ?string
    {
        return match($property) {
            'invoice' => Invoice::class,
            default => null
        };
    }


}