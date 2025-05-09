<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Dompet;

class Penitip extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    protected $table = 'penitip'; 
    protected $primaryKey = 'idPenitip';
    protected $keyType = 'string'; 
    public $timestamps = false;   
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'idPenitip',
        'idTopSeller',
        'idDompet',
        'username',
        'password',
        'namaPenitip',
        'nik',
        'alamat'
    ];

     public function dompet(){
        return $this->belongsTo(Dompet::class, 'idDompet', 'idDompet');
    }

}
