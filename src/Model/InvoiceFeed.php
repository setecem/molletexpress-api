<?php

namespace App\Model;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;
use DateTime;

class InvoiceFeed  extends Model
{

    const string|Base ENTITY = \App\Entity\InvoiceFeed::class;
    public ?int $id = 0;
    public ?Invoice $invoice = null;
    public ?string $type = null;
    public ?string $description = null;
    public DateTime|string|null $date = null;
    public bool $private = true;

    public function typeOfCollection(string $property): ?string
    {
        return match($property) {
            'invoice' => Invoice::class,
            default => null
        };
    }

}