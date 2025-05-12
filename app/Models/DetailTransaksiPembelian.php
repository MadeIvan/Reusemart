<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksiPembelian extends Model
{
    use HasFactory;

    protected $table = 'detailtransaksipembelian'; 
    public $timestamps = false;   

    protected $fillable = [
        'noNota',
        'idBarang',
    ];

    public function barang(){
        return $this->belongsTo(Barang::class, 'idBarang', 'idBarang');
    }

    public function transaksiPembelian(){
        return $this->belongsTo(transaksiPembelian::class, 'noNota', 'noNota');
    }

}
