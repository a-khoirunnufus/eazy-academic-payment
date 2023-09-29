<?php

namespace App\Enums\Payment;

enum BalanceTransType:string {
    case OverpaidBill = 'overpaid_bill';
    case PayBill = 'pay_bill';
    case CancelPayBill = 'cancel_pay_bill';
    case Withdraw = 'withdraw';
}
