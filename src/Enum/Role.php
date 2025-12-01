<?php

namespace App\Enum;

enum Role: string
{
    case ACCESS = 'ACCESS';
    case EDIT = 'EDIT';
    case ACTIVE = 'ACTIVE';
    case DELETE = 'DELETE';
    case FILES = 'FILES';
    case CREATE = 'CREATE';
    case FEED = 'FEED';
    case PRIVATE = 'PRIVATE';
    case VIEW_ALL = 'VIEW_ALL';
    case VIEW = 'VIEW';
    case ROLES = 'ROLES';
    case DOWNLOAD = 'DOWNLOAD';
    case PAID_STATUS = 'PAID_STATUS';
    case COMERCIAL_EDIT = 'COMERCIAL_EDIT';
    case ORIGEN_EDIT = 'ORIGEN_EDIT';
    case ORIGEN_HIDE = 'ORIGEN_HIDE';
    case TYPE_EDIT = 'TYPE_EDIT';
    case DNI_EMPTY = 'DNI_EMPTY';
    case FEED_DELETE = 'FEED_DELETE';
    case FEED_ALERT = 'FEED_ALERT';
    case FEED_CREATE = 'FEED_CREATE';
    case FEED_PRIVATE = 'FEED_PRIVATE';
    case ALERT_CREATE = 'ALERT_CREATE';
    case RECEIVE_EMAIL_CREATE = 'RECEIVE_EMAIL_CREATE';
    case RECEIVE_EMAIL_EDIT = 'RECEIVE_EMAIL_EDIT';
    case RECEIVE_EMAIL_DELETE = 'RECEIVE_EMAIL_DELETE';
    case SEND_EMAIL = 'SEND_EMAIL';
}