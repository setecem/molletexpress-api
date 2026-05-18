<?php

namespace App\Enum;

enum RoleGroup: string
{

    case EMPLOYEE = 'EMPLOYEE';
    case CLIENT = 'CLIENT';
    case INVOICE = 'INVOICE';
    case DELIVERY_NOTE = 'DELIVERY_NOTE';
    case SERVICE = 'SERVICE';
    case CHARGE_ORDER = 'CHARGE_ORDER';

    public static function rolesEmployee(): array
    {
        return [
            self::EMPLOYEE->value => [
                Role::ACCESS,
                Role::EDIT,
                Role::DELETE,
                Role::FILES,
                Role::CREATE,
                Role::PRIVATE,
                Role::VIEW_ALL,
                Role::VIEW,
                Role::ROLES
            ]
        ];
    }

    public static function rolesClient(): array
    {
        return [
            self::CLIENT->value => [
                Role::ACCESS,
                Role::EDIT,
                Role::DELETE,
                Role::CREATE,
                Role::VIEW,
                Role::ACTIVE
            ]
        ];
    }

    public static function rolesInvoice(): array
    {
        return [
            self::INVOICE->value => [
                Role::ACCESS,
                Role::EDIT,
                Role::DELETE,
                Role::CREATE,
                Role::VIEW,
                Role::DOWNLOAD,
                Role::SEND_EMAIL,
                Role::ACTIVE,
                Role::PAID_STATUS,
                Role::EXPORT,
                Role::LIST,
            ]
        ];
    }

    public static function rolesDeliveryNote(): array
    {
        return [
            self::DELIVERY_NOTE->value => [
                Role::ACCESS,
                Role::EDIT,
                Role::DELETE,
                Role::CREATE,
                Role::VIEW,
                Role::DOWNLOAD,
                Role::SEND_EMAIL,
                Role::INVOICE,
                Role::EXPORT,
                Role::LIST,

            ]
        ];
    }

    public static function rolesService(): array
    {
        return [
            self::SERVICE->value => [
                Role::ACCESS,
                Role::EDIT,
                Role::DELETE,
                Role::CREATE,
                Role::VIEW,
                Role::ACTIVE
            ]
        ];
    }

    public static function rolesChargeOrder(): array
    {
        return [
            self::CHARGE_ORDER->value => [
                Role::ACCESS,
                Role::EDIT,
                Role::DELETE,
                Role::CREATE,
                Role::VIEW,
                Role::ACTIVE,
                Role::DOWNLOAD,
                Role::PAID_STATUS
            ]
        ];

    }

    /**
     * @return array<string, Role[]>
     */
    public static function all(): array
    {
        return array_merge(
            self::rolesEmployee(),
            self::rolesClient(),
            self::rolesInvoice(),
            self::rolesDeliveryNote(),
            self::rolesService(),
            self::rolesChargeOrder()
        );
    }
}
