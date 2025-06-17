<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiPembelian;
class DetailTransaksiPembelianController extends Controller
{
    //
    public function transaksiPembelian()
{
    return $this->belongsTo(TransaksiPembelian::class, 'idTransaksiPembelian', 'idTransaksiPembelian');
}
}
