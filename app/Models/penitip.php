<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens; 




class Penitip extends Authenticatable
{
    use HasFactory, HasApiTokens;
    protected $table = 'penitip'; 
    public $timestamps = false;   

    protected $fillable = [
        'idPenitip',
        'nik',
        'idTopeseller',
        'idDompet',
        'username',
        'password',
        'namaPenitip'
    ];
}
