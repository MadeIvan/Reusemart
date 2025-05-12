<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPembelian extends Model
{
    use HasFactory;

    protected $table = 'transaksipembelian'; 
    protected $primaryKey = 'noNota';
    protected $keyType = 'string'; 
    public $timestamps = false;   

    protected $fillable = [
        'noNota',
        'idPembeli',
        'idPegawai1',
        'idPegawai2',
        'idAlamat',
        'tanggalWaktuPembelian',
        'tanggalWaktuPelunasan',
        'tanggalPengirimanPengambilan',
        'status',
        'totalHarga'
    ];

    public function pegawai(){
        return $this->hasMany(Pegawai::class, 'idPegawai1', 'idPegawai');
    }

    public function pegawai2(){
        return $this->hasMany(Pegawai::class, 'idPegawai2', 'idPegawai');
    }

    public function pembeli(){
        return $this->belongsTo(Pembeli::class, 'idPembeli', 'idPembeli');
    }

    public function detailTransaksiPembelian(){
        return $this->hasMany(DetailTransaksiPembelian::class, 'noNota', 'noNota');
    }
}
