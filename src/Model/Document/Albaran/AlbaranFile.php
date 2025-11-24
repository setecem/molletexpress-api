<?php

namespace App\Model\Document\Albaran;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;

class AlbaranFile extends Model
{

    const string|Base ENTITY = \App\Entity\Document\Albaran\AlbaranFile::class;
    public ?string$extension = null;
    public ?string $name = null;
    public ?Albaran $albaran = null;
    public int $size = 0;
    public ?string $type = null;
    public ?string $mimetype = null;
    public bool $active = true;
    public bool $private = true;

    public function typeOfCollection(string $property): ?string
    {
        return match($property) {
            'albaran' => Albaran::class,
            default => null
        };
    }


}