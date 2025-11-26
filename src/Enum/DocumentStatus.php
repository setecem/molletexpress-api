<?php

namespace App\Enum;

enum DocumentStatus: string
{
    case DRAFT = 'DRAFT';
    case ACTIVE = 'ACTIVE';
    case RECTIFIED = 'RECTIFIED';
    case CANCELED = 'CANCELED';

}