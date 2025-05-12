<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestDonasi extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'requestDonasi';

    // Primary key (if not 'id')
    protected $primaryKey = 'idRequest';

    // Disable timestamps if they aren't present in the table
    public $timestamps = false;

    // Fillable fields for mass assignment
    protected $fillable = [
        'idRequest',
        'idTransaksiDonasi',
        'idOrganisasi',
        'barangRequest',
        'tanggalRequest',
        'status',
    ];

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'idOrganisasi', 'idOrganisasi');
    }
}
