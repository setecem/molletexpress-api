<?php

namespace App\Enum;

use Cavesman\Interface\Locale;

enum Language: string implements Locale
{
    case en = 'en';
    case es = 'es';
}
