<?php
  
namespace App\Payment\Enums;
 
enum ComponentType:int {
    case Default = 1;
    case SKS = 2;
    case Matkul = 3;
    case Paket = 4;
}