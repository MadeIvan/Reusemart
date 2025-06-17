<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Komisi extends Model
{
    use HasFactory;

    protected $table = 'komisi'; 
    protected $primaryKey = 'idKomisi';
    protected $keyType = 'string'; 
    public $incrementing = false;
    public $timestamps = false;   

    protected $fillable = [
        'idKomisi',
        'noNota',
        'idBarang', 
        'komisiMart', 
        'komisiHunter', 

        'komisiPenitip',
    ];


    public function transaksiPembelian(){

        return $this->belongsTo(TraansaksiPembelian::class, 'noNota', 'noNota');

       // return $this->hasMany(TransaksiPembelian::class, 'noNota', 'noNota');

    }

    public function barang(){
        return $this->belongsTo(Barang::class, 'idBarang', 'idBarang');
    }

