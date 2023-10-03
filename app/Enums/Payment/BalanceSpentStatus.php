<?php

namespace App\Enums\Payment;

enum BalanceSpentStatus:string {
    case Reserved = 'reserved';
    case Used = 'used';
    case Canceled = 'canceled';
}
