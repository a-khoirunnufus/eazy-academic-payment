<?php

namespace App\Enums\Curriculum;

use ArchTech\Enums\InvokableCases;

enum DocumentFileName: string
{
    use InvokableCases;

    case book_document = 'Dokumen Buku Kurikulum';
    case sk_document = 'Dokumen SK';
    case report_document = 'Dokumen Laporan';
    case ba_document = 'Dokumen BA';
}