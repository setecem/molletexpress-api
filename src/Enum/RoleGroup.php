<?php

namespace App\Enum;

enum RoleGroup: string
{
    case DASHBOARD = 'DASHBOARD';
    case BOOKING = 'BOOKING';
    case WAREHOUSE = 'WAREHOUSE';
    case SCHEDULE = 'SCHEDULE';
    case EMPLOYEE = 'EMPLOYEE';
    case CLIENT = 'CLIENT';
    case CONTACT = 'CONTACT';
    case INVOICE = 'INVOICE';
    case DELIVERY_NOTE = 'DELIVERY_NOTE';
    case REPORT = 'REPORT';
    case DEVICE = 'DEVICE';
    case LOG = 'LOG';
    case FEED = 'FEED';
    case ALERT = 'ALERT';
    case COMERCIAL = 'COMERCIAL';
    case ORIGEN = 'ORIGEN';
    case TYPE = 'TYPE';
    case DNI = 'DNI';

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

    public static function rolesContact(): array
    {
        return [
            self::CONTACT->value => [
                Role::ACCESS,
                Role::EDIT,
                Role::DELETE,
                Role::FILES,
                Role::CREATE,
                Role::FEED,
                Role::VIEW_ALL,
                Role::VIEW,
                Role::PRIVATE,
                Role::COMERCIAL_EDIT,
                Role::ORIGEN_EDIT,
                Role::ORIGEN_HIDE,
                Role::TYPE_EDIT,
                Role::DNI_EMPTY,
                Role::FEED_ALERT,
                Role::FEED_CREATE,
                Role::FEED_DELETE,
                Role::FEED_PRIVATE,
                Role::ALERT_CREATE,
                Role::RECEIVE_EMAIL_CREATE,
                Role::RECEIVE_EMAIL_EDIT,
                Role::RECEIVE_EMAIL_DELETE
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
                Role::FILES,
                Role::CREATE,
                Role::FEED,
                Role::VIEW_ALL,
                Role::VIEW,
                Role::PRIVATE,
                Role::COMERCIAL_EDIT,
                Role::ORIGEN_EDIT,
                Role::ORIGEN_HIDE,
                Role::TYPE_EDIT,
                Role::DNI_EMPTY,
                Role::FEED_ALERT,
                Role::FEED_CREATE,
                Role::FEED_DELETE,
                Role::FEED_PRIVATE,
                Role::ALERT_CREATE,
                Role::RECEIVE_EMAIL_CREATE,
                Role::RECEIVE_EMAIL_EDIT,
                Role::RECEIVE_EMAIL_DELETE
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
                Role::FILES,
                Role::CREATE,
                Role::FEED,
                Role::VIEW_ALL,
                Role::VIEW,
                Role::PRIVATE,
                Role::COMERCIAL_EDIT,
                Role::ORIGEN_EDIT,
                Role::ORIGEN_HIDE,
                Role::TYPE_EDIT,
                Role::DNI_EMPTY,
                Role::FEED_ALERT,
                Role::FEED_CREATE,
                Role::FEED_DELETE,
                Role::FEED_PRIVATE,
                Role::ALERT_CREATE,
                Role::RECEIVE_EMAIL_CREATE,
                Role::RECEIVE_EMAIL_EDIT,
                Role::RECEIVE_EMAIL_DELETE
            ]
        ];
    }
}
