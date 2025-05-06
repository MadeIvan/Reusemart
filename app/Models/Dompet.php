<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dompet extends Model
{
    protected $table = 'dompet'; // Make sure this matches your actual table name
    protected $primaryKey = 'idDompet'; // Specify the custom primary key
    public $incrementing = false; // If idDompet is not auto-incrementing
    protected $keyType = 'string'; // If idDompet is a string (looks like it's numeric from your screenshot)
    public $timestamps = false; // If your table doesn't use timestamps

    protected $fillable = [
        'idDompet',
        'idPegawai',
        'idPenitip',
        'saldo'
    ];
}
