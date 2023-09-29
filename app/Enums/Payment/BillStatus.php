<?php

namespace App\Enums\Payment;

enum BillStatus:string {
    case PaidOff = 'lunas';
    case NotPaidOff = 'belum lunas';
    case Credit = 'kredit';
}
