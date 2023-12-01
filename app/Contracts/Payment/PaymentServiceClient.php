<?php

namespace App\Payment\Contracts;

interface PaymentServiceClient
{
    public function chargeTransaction(array $config): bool;
    public function statusTransaction();
    public function cancelTransaction();
}
