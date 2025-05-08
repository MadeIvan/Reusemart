<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penitip extends Model
{
    use HasFactory;
    protected $table = 'penitip'; 
    public $timestamps = false;   

    protected $fillable = [
        'idPenitip',
        'idTopeseller',
        'idDompet',
        'usernama',
        'password',
        'namaPenitip'
    ];
}
