<?php

namespace App\Model\Document\Albaran;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;
use DateTime;

class AlbaranFeed  extends Model
{

    const string|Base ENTITY = \App\Entity\Document\Albaran\AlbaranFeed::class;
    public ?int $id = 0;
    public ?Albaran $albaran = null;
    public ?string $type = null;
    public ?string $description = null;
    public DateTime|string|null $date = null;
    public bool $private = true;

    public function typeOfCollection(string $property): ?string
    {
        return match($property) {
            'albaran' => Albaran::class,
            default => null
        };
    }

}