<?php

namespace App\Model;

use Cavesman\Db\Doctrine\Entity\Base;
use Cavesman\Db\Doctrine\Model\Model;
use DateTime;

class DeliveryNote extends Model
{

    const string|Base ENTITY = \App\Entity\DeliveryNote::class;

    public ?string $ref = null;
    public ?Client $client = null;
    public ?string $comment = null;
    public int $subtotal = 0;
    public ?DateTime $date = null;


    // Clonados de contact

    public ?Employee $employee = null;
    public ?string $enterprise = null;
    public ?string $enterprise_phone = null;
    public ?string $enterprise_phone_ext = null;
    public ?string $enterprise_mobile = null;
    public ?string $enterprise_mobile_ext = null;
    public ?string $enterprise_email = null;
    public ?string $sector = null;
    public ?string $facturacion = null;
    public ?string $web = null;
    public ?string $nif = null;
    public ?string $address = null;
    public bool $authorization = false;
    public ?string $type = null;
    public ?string $origen = null;
    public ?string $description = null;
    public bool $has_recursos = false;
    public bool $multiexpedicion = false;
    public ?string $almacenes = null;
    public ?string $muelles = null;
    public ?string $recursos = null;

    /** @var DeliveryNoteContact[] $contacts */
    public array $contacts = [];

    /** @var File[] $files */
    public array $files = [];

    public function typeOfCollection(string $property): ?string
    {
        return match($property) {
            'employee' => Employee::class,
            'client' => Client::class,
            'contacts' => DeliveryNoteContact::class,
            'files' => File::class,
            default => null
        };
    }
}