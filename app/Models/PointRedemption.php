<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointRedemption extends Model
{
    use HasFactory;

    protected $table = 'point_redemptions';

    // Fillable fields for mass assignment
    protected $fillable = [
        'idPembeli',
        'points_used',
        'transaction_id',
    ];

    // If your table uses default timestamps (created_at, updated_at), leave as is
    // Otherwise, set this to false
    public $timestamps = true;

    // Define any relationships here if needed
    // Example: redemption belongs to pembeli (buyer)
    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'idPembeli', 'idPembeli');
    }
}
