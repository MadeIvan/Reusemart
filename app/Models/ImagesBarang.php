<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagesBarang extends Model
{
    use HasFactory;

    protected $table = 'imagesbarang';

    protected $primaryKey = 'id';

    public $incrementing = false; // Because id is connected to barang.image (string or non-auto-increment)

    protected $keyType = 'string'; // match barang.image type (adjust if different)

    public $timestamps = false;

    protected $fillable = [
        'id',
        'image1',
        'image2',
        'image3',
        'image4',
        'image5',
    ];

    // Inverse relation: ImagesBarang belongs to one Barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id', 'image');
    }
}