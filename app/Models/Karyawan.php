<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'Karyawan';
    protected $primaryKey = 'Kode_Karyawan';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    public function user()
    {
        return $this->hasOne(User::class, 'UserID_Web', 'Id_Users');
    }
}
