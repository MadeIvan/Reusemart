<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pembeli;


class Alamat extends Model
{
    protected $table = 'alamat';
    protected $primaryKey = 'idAlamat';
    protected $keyType = 'string'; 
    public $timestamps = false;

    protected $fillable = [
        'idAlamat',
        'idPembeli',
        'alamat',
        'kategori',
        'isDefault',
        'nama'
    ];

    public function pembeli(){
        return $this->belongsTo(Pembeli::class, 'idPembeli');
    }
}
