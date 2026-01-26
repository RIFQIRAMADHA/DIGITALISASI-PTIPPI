<?php

namespace App\Models\Produksi\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'ms_karyawan'; // sesuai validasi kamu
    protected $primaryKey = 'idKaryawan';
    protected $keyType = 'string';
    public $incrementing = false; // karena pakai id manual
    public $timestamps = false;
    

    protected $fillable = [
        'idKaryawan',
        'NamaKaryawan',
        'NRPKaryawan',
        'Jabatan',
        'Status'
    ];
}
