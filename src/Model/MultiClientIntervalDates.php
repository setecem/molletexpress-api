<?php

namespace App\Model;

use Cavesman\Db\Doctrine\Model\Model;
use DateTime;
use Doctrine\Common\Collections\Collection;

class MultiClientIntervalDates extends Model
{
    public DateTime|string|null $start = null;
    public DateTime|string|null $end = null;
    public array|Collection $clients = [];

    public function typeOfCollection(string $property): ?string
    {
        return match ($property) {
            'clients' => Client::class,
            default => null
        };
    }


}