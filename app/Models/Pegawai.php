<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
class Pegawai extends Model
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    protected $table = 'pegawai';
    protected $primaryKey = 'idPegawai';
    protected $keyType = 'string'; 
    public $timestamps = false;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'idPegawai',
        'idJabatan',
        'idDompet',
        'namaPegawai',
        'tanggalLahir',
        'username',
        'password',
        'deleted_at'
    ];

    public function jabatan(){
        return $this->belongsTo(Jabatan::class, 'idJabatan', 'idJabatan');
    }

    public function dompet(){
        return $this->belongsTo(Dompet::class, 'idDompet', 'idDompet');
    }
}
