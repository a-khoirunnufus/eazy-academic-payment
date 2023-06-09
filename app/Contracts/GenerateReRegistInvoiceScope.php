<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface GenerateReRegistInvoiceScope {
    public function getFilters(): array;
}
