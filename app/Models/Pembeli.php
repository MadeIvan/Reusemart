<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Alamat;
use App\Models\Diskusi;
use App\Models\TransaksiPembelian;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;



class Pembeli extends Authenticatable implements CanResetPassword
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens, CanResetPasswordTrait;

    protected $table = 'pembeli';
    protected $primaryKey = 'idPembeli';
    protected $keyType = 'string'; 
    public $timestamps = false;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'idPembeli',
        'username',
        'password',
        'poin',
        'namaPembeli',
        'email',
    ];

    public function alamat(){
        return $this->hasMany(Alamat::class, 'idPembeli', 'idPembeli');
    }

    public function diskusi(){
        return $this->hasMany(Diskusi::class, 'idPembeli', 'idPembeli');
    }

    public function transaksiPembelian(){
        return $this->hasMany(TransaksiPembelian::class, 'idPembeli', 'idPembeli');
    }
}
