<?php

namespace App\Http\Controllers\_Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Notifications\Whatsapp;

class WhatsAppController extends Controller
{
    use Whatsapp;

    public function NotificationWhatsappPaymentInvoice()
    {
        $recipientNumber = 'whatsapp:+6285794832299';
        $message = "Halo Hafizh,\n\nIni adalah notifikasi tagihan pada aplikasi EAZY anda.\n\nNomor Invoice: #123456\nTotal Tagihan: Rp. 500.000\n\nMohon segera lakukan pembayaran sebelum tanggal jatuh tempo pada Selasa, 15 Agustus 2024.\n\nTerima kasih atas kerjasama Anda.\nEAZY - Education Smart System";
        $this->sendWhatsAppMessage($recipientNumber,$message);
    }
}
