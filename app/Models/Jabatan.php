<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use app\Models\Pegawai;

class Jabatan extends Model
{
    use HasFactory;
    protected $table = 'jabatan';
    protected $primaryKey = 'idJabatan';
    protected $keyType = 'string'; 
    public $timestamps = false;

    protected $fillable = [
        'idJabatan',
        'namaJabatan',
    ];

    public function pegawai(){
        return $this->hasMany(Pegawai::class, 'idJabatan', 'idJabatan');
    }
}
