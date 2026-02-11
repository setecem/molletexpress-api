<?php

namespace App\Model;

use DateTime;

class FacturarIntervalDates extends MultiClientIntervalDates
{
    public DateTime|string|null $dateInvoice = null;


}