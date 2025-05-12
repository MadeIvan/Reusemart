<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Barang;
use App\Models\Pembeli;
use App\Models\Pegawai;

class Diskusi extends Model
{
    protected $table = 'diskusi';
    protected $primaryKey = 'idDiskusi';
    protected $keyType = 'string'; 
    public $timestamps = false;

    protected $fillable = [
        'idDiskusi',
        'idPembeli',
        'idBarang',
        'idPegawai',
        'pesandiskusi',
        'tanggalDiskusi',
        'waktuMengirimDiskusi',
    ];

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'idPembeli', 'idPembeli');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'idPegawai', 'idPegawai');
    }


    public function barang(){
        return $this->belongsTo(Barang::class, 'idBarang', 'idBarang');
    }


}
