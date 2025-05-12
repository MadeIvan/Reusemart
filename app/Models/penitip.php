<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Penitip extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'penitip';
    protected $primaryKey = 'idPenitip';
    public $incrementing = true;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'nik',
        'idTopeseller',
        'idDompet',
        'username',
        'password',
        'namaPenitip',
        'idPenitip',
        'deleted_at',

    ];

    protected $hidden = [
        'password',
    ];

     public function dompet(){
        return $this->belongsTo(Dompet::class, 'idDompet', 'idDompet');
    }


}

