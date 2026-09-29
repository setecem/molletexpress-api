<?php

namespace App\Entity;

use Cavesman\Db\Doctrine\Entity\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Vinculación con una empresa de DeCA (Projects/deca) a través de su API pública.
 *
 * La empresa de DeCA sale de la propia clave, así que vincular = guardar una clave de API
 * creada en DeCA (Configuración → Claves de API). Solo hay una vinculación activa.
 *
 * La clave se guarda cifrada ({@see \App\Service\Deca::encrypt()}) y nunca vuelve al navegador:
 * solo su prefijo.
 */
#[ORM\Table(name: 'deca_connection')]
#[ORM\Entity]
class DecaConnection extends Entity
{
    /** Dirección base de DeCA, sin `/api/public/v1` (p. ej. https://deca.setecem.com). */
    #[ORM\Column(name: 'url', type: 'string', length: 255, nullable: false)]
    public string $url = '';

    /** Clave completa (`prefijo.secreto`), cifrada. */
    #[ORM\Column(name: 'api_key', type: 'text', nullable: false)]
    public string $apiKey = '';

    /** Parte pública de la clave (`dk_xxxx`), para reconocerla en DeCA sin enseñar el secreto. */
    #[ORM\Column(name: 'key_prefix', type: 'string', length: 64, nullable: true)]
    public ?string $keyPrefix = null;

    /** Empresa de DeCA a la que pertenece la clave (según `/ping`). */
    #[ORM\Column(name: 'enterprise', type: 'string', length: 255, nullable: true)]
    public ?string $enterprise = null;

    /** Tercero de DeCA que es la propia Mollet Express (transportista o cargador según el albarán). */
    #[ORM\Column(name: 'own_partner_id', type: 'integer', nullable: true)]
    public ?int $ownPartnerId = null;

    #[ORM\Column(name: 'own_partner_name', type: 'string', length: 255, nullable: true)]
    public ?string $ownPartnerName = null;

    #[ORM\Column(name: 'last_check', type: 'datetime', nullable: true)]
    public ?DateTime $lastCheck = null;

    /** Último error al hablar con DeCA; null si la última llamada fue bien. */
    #[ORM\Column(name: 'last_error', type: 'text', nullable: true)]
    public ?string $lastError = null;
}
