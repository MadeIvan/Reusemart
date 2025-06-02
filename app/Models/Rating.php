<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $table = 'rating';

    // Since no primary key is set on the table:
    protected $primaryKey = null;
    public $incrementing = false;

    public $timestamps = false;

    // Allow mass assignment on these fields
    protected $fillable = [
        'idPenitip',
        'idBarang',
        'idRater',
        'value',
    ];
}
