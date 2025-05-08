<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Monolog\Handler\TelegramBotHandler;

class Barang extends Model 
{
    use HasFactory;
    protected $table = 'barang';
    protected $keyType = 'string';
    protected $primaryKey ='idBarang';
    protected $fillable = [
        'idBarang' ,
        'idTransaksiDonasi',
        'namaBarang',
        'beratBarang',
        'garansiBarang',
        'periodeGaransi',
        'hargaBarang',
        'haveHunter',
        'statusBarang',
        'image',
        'kategori',
    ];

}
