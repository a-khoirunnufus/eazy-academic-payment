<?php

namespace App\Enums\Payment;

enum LogStatus:int {
    case Success = 1;
    case Process = 2;
    case Failed = 3;
}
