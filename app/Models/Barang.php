<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Monolog\Handler\TelegramBotHandler;
use App\Models\Diskusi;
use App\Models\ImagesBarang;

class Barang extends Model 
{
    use HasFactory;
    protected $table = 'barang';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey ='idBarang';
    public $timestamps = false;


    protected $fillable = [
        'idBarang',
        'idTransaksiDonasi',
        'namaBarang',
        'beratBarang',
        'garansiBarang',
        'periodeGaransi',
        'hargaBarang',
        'haveHunter',
        'statusBarang',
        'image',
        'kategori'
    ];

    public function diskusi()
    {
        return $this->hasMany(Diskusi::class, 'idBarang');
    }

    public function detailTransaksiPembelian()
    {
        return $this->hasMany(DetailTransaksiPembelian::class, 'idBarang', 'idBarang');
    }

    public function transaksiPembelian()
    {
        return $this->belongsTo(TransaksiPembelian::class, 'noNota', 'noNota');
    }

    public function detailTransaksiPenitipan()
    {
        return $this->hasOne(DetailTransaksiPenitipan::class, 'idBarang', 'idBarang');
    }
    public function imagesbarang()
    {
        return $this->hasOne(ImagesBarang::class, 'id', 'image');
    }
}
