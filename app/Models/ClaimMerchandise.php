<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClaimMerchandise extends Model
{
    protected $table = 'claimMerchandise';
    protected $primaryKey = 'idClaim';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'idClaim',
        'idPegawai',
        'idMerchandise',
        'idPembeli',
        'tanggalAmbil',
    ];

    // Relationships
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'idPegawai', 'idPegawai');
    }

    public function merchandise()
    {
        return $this->belongsTo(Merchandise::class, 'idMerchandise', 'idMerchandise');
    }

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'idPembeli', 'idPembeli');
    }
}
