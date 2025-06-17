<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopSeller extends Model
{
    use HasFactory;

    // Specify the table name if it is different from the model name
    protected $table = 'topseller';

    // Define the primary key (optional if it's 'id')
    protected $primaryKey = 'idTopSeller';

    // Allow mass assignment for the fields
    protected $fillable = [
        'idTopSeller',
        'idPenitip',
        'nominal',
    ];

    // If timestamps are not used
    public $timestamps = false;

    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'idPenitip', 'idPenitip');
    }
}
