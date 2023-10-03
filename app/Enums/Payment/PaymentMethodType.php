<?php

namespace App\Enums\Payment;

enum PaymentMethodType:string {
    case BankTransferManual = 'bank_transfer_manual';
    case BankTransferVA = 'bank_transfer_va';
    case BankTransferBillPayment = 'bank_transfer_bill_payment';
}
