<?php

namespace App\Model;

use App\Enum\FileType;
use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;

final class File extends Model
{

    const string|Base ENTITY = \App\Entity\File::class;

    public ?string $name = null;
    public ?string $extension = null;
    public int $size = 0;
    public ?string $mime = null;
    public ?string $mimeType = null;
    public FileType $type = FileType::GENERAL;
    public bool $private = true;

    public function typeOfCollection(string $property): ?string
    {
        return match ($property) {
            'employee' => Employee::class,
            'contact' => Contact::class,
            'deliveryNote' => DeliveryNote::class,
            'invoice' => Invoice::class,
            'type' => FileType::class,
            default => null
        };
    }

}
