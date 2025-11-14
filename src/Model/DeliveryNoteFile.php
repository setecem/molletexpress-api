<?php

namespace App\Model;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;

class DeliveryNoteFile extends Model
{

    const string|Base ENTITY = \App\Entity\DeliveryNoteFile::class;
    public ?string$extension = null;
    public ?string $name = null;
    public ?DeliveryNote $deliveryNote = null;
    public int $size = 0;
    public ?string $type = null;
    public ?string $mimetype = null;
    public bool $active = true;
    public bool $private = true;

    public function typeOfCollection(string $property): ?string
    {
        return match($property) {
            'deliveryNote' => DeliveryNote::class,
            default => null
        };
    }


}