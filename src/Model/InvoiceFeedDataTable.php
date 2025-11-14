<?php

namespace App\Model;

class InvoiceFeedDataTable
{
    /** @var InvoiceFeed[] $data */
    public array $data = [];
    public int $recordsTotal = 0;
    public int $recordsFiltered = 0;
}