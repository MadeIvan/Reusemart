<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiDonasi extends Model
{

    use HasFactory;

    // Define the table name if it doesn't match Laravel's plural convention
    protected $table = 'transaksiDonasi';

    // Define the primary key if it's not 'id'
    protected $primaryKey = 'idTransaksiDonasi';

    // Set timestamps to false if you don't have 'created_at' and 'updated_at' columns
    public $timestamps = false;

    // Fillable fields for mass assignment
    protected $fillable = [
        'idTransaksiDonasi',
        'idBarang',
        'idRequest',
        'namaPenerima',
        'tanggalDonasi',
    ];


    // If you want to add custom dates (like tanggalDonasi as a Carbon instance), you can define it:
    protected $dates = ['tanggalDonasi'];
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'idBarang', 'idBarang');
    }

    public function requestDonasi()
    {
        return $this->belongsTo(RequestDonasi::class, 'idRequest', 'idRequest');
    }
}
