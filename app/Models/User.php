<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $table = 'KPI_Users';
    protected $primaryKey = 'Id_Users';
    public $timestamps = false;

    protected $fillable = [
        'Username',
        'Password',
        'Role',
        'Email',
        'Kode_Users',
    ];

    protected $hidden = [
        'Password',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->Password;
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'Id_Users', 'UserID_Web');
    }

    public function memilikiJabatan(string $page): bool
    {
        return true;
    }
}
