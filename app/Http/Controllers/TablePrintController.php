<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\View\View;

class TablePrintController extends Controller
{
    public function printQr(Table $table): View
    {
        return view('tables.table-qr-print', [
            'table' => $table,
            'qrUrl' => $table->qr_url,
        ]);
    }
}
