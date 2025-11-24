<?php

namespace App\Model;

use App\Enum\FileType;
use App\Model\Contact\Contact;
use App\Model\Document\Albaran\Albaran;
use App\Model\Document\Factura\Factura;
use App\Model\Employee\Employee;
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
            'albaran' => Albaran::class,
            'factura' => Factura::class,
            'type' => FileType::class,
            default => null
        };
    }

}
