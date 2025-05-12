<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReqDonasi extends Model
{
    use HasFactory;

    // The table associated with the model.
    protected $table = 'requestdonasi';
    public $timestamps = false;
    // The primary key associated with the table.
    protected $primaryKey = 'idRequest';

    // The attributes that are mass assignable.
    protected $fillable = [
        'idRequest', 
        'idTransaksiDonasi', 
        'idOrganisasi', 
        'barangRequest', 
        'tanggalRequest', 
        'status'
    ];

    // The data type of the primary key
    protected $keyType = 'string'; 

    // Whether the primary key is auto-incrementing
    public $incrementing = false;

    // Relationship with TransaksiDonasi
    public function transaksiDonasi()
    {
        return $this->belongsTo(TransaksiDonasi::class, 'idTransaksiDonasi', 'idTransaksiDonasi');
    }

    // Relationship with Organisasi
    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'idOrganisasi', 'idOrganisasi');
    }
}
