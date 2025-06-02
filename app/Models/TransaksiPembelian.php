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
        'idPegawai3',
        'idAlamat',
        'tanggalWaktuPembelian',
        'tanggalWaktuPelunasan',
        'tanggalPengirimanPengambilan',
        'status',
        'totalHarga'
    ];

    public function pegawai(){
        return $this->belongsTo(Pegawai::class, 'idPegawai1', 'idPegawai')
            ->select(['idPegawai', 'namaPegawai']);
    }
    public function pegawai2(){
        return $this->belongsTo(Pegawai::class, 'idPegawai2', 'idPegawai')
        ->select(['idPegawai', 'namaPegawai']);
    }
        public function pegawai3(){
        return $this->belongsTo(Pegawai::class, 'idPegawai3', 'idPegawai')
        ->select(['idPegawai', 'namaPegawai']);
    }

    public function pembeli(){
        return $this->belongsTo(Pembeli::class, 'idPembeli', 'idPembeli');
    }

    public function detailTransaksiPembelian(){
        return $this->hasMany(DetailTransaksiPembelian::class, 'noNota', 'noNota');
    }
}
