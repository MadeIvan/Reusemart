<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pegawai;
use App\Models\Penitip;
use App\Models\DetailTransaksiPenitipan;

class TransaksiPenitipan extends Model
{
    use HasFactory;

    protected $table = 'transaksipenitipan'; 
    protected $primaryKey = 'idTransaksiPenitipan';
    protected $keyType = 'string'; 
    public $timestamps = false;   

    protected $fillable = [
        'idTransaksiPenitipan',
        'idPegawai1',
        'idPegawai2',
        'idPenitip',
        'tanggalPenitipan',
        'tanggalPenitipanSelesai',
        'totalHarga',
    ];

    public function pegawai(){
        return $this->belongsTo(Pegawai::class, 'idPegawai1', 'idPegawai');
    }

    public function pegawai2(){
        return $this->belongsTo(Pegawai::class, 'idPegawai2', 'idPegawai');
    }

    public function penitip(){
        return $this->belongsTo(Penitip::class, 'idPenitip', 'idPenitip');
    }

    public function detailTransaksiPenitipan(){
        return $this->hasMany(DetailTransaksiPenitipan::class, 'idTransaksiPenitipan', 'idTransaksiPenitipan');
    }
}
