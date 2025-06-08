<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merchandise extends Model
{
    protected $table = 'merchandise';
    protected $primaryKey = 'idMerchandise';
    public $incrementing = false; // because varchar PK is not auto-increment
    protected $keyType = 'string'; // because varchar PK

    public $timestamps = false; // No created_at and updated_at columns

    protected $fillable = [
        'idMerchandise',
        'nama',
        'harga',
        'jumlahSatuan',
    ];
}
