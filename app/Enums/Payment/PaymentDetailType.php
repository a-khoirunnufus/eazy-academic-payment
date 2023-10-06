<?php

namespace App\Enums\Payment;

enum PaymentDetailType:string {
    case Component = 'component';
    case Discount = 'discount';
    case Scholarship = 'scholarship';
}
