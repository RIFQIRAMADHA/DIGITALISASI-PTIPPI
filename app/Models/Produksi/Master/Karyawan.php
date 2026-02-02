<?php

namespace App\Models\Produksi\Master;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Karyawan extends Authenticatable
{
    use HasFactory;

    protected $table = 'prod_msKaryawan';

    // LOGIN pakai NRP
    protected $primaryKey = 'NRPKaryawan';
    protected $keyType = 'string';
    public $incrementing = false;

    public $timestamps = true;

    protected $fillable = [
        'IdKaryawan',
        'NamaKaryawan',
        'NRPKaryawan',
        'PasswordKaryawan',
        'Jabatan',
        'Status',
        'create_by',
        'update_by'
    ];

    protected $hidden = [
        'PasswordKaryawan'
    ];

    /**
     * Laravel Auth ambil password dari kolom ini
     */
    public function getAuthPassword()
    {
        return $this->PasswordKaryawan;
    }

    /**
     * 🔥 PENTING
     * Route (view/edit/delete) pakai IdKaryawan
     */
    public function getRouteKeyName()
    {
        return 'IdKaryawan';
    }
}
