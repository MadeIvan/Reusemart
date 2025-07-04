<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksiPenitipan extends Model
{
    use HasFactory;

    protected $table = 'detailtransaksipenitipan'; 
    public $timestamps = false;   

    protected $fillable = [
        'idTransaksiPenitipan',
        'idBarang',
    ];

    public function barang(){
        return $this->belongsTo(Barang::class, 'idBarang', 'idBarang');
    }

    public function transaksiPenitipan(){
        return $this->belongsTo(TransaksiPenitipan::class, 'idTransaksiPenitipan', 'idTransaksiPenitipan');
    }

}
