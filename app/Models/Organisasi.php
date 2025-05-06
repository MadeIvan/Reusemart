<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Organisasi extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    protected $table = 'organisasi';
    protected $primaryKey = 'idOrganisasi';
    protected $keyType = 'string'; 
    public $timestamps = false;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'idOrganisasi',
        'username',
        'password',
        'namaOrganisasi',
        'alamat',
    ];
}
