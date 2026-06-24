<?php

namespace App\Models\Produksi\Detail;

use Illuminate\Database\Eloquent\Model;

class DetailIdleTime extends Model
{
    // Nama tabel sesuai di database
    protected $table = 'prod_detailidletime';
    
    // Disamakan dengan contoh: Nonaktifkan Primary Key karena tidak ada kolom ID tunggal
    protected $primaryKey = null; 
    public $incrementing = false;

    // Kolom yang boleh diisi sesuai ERD abang
    protected $fillable = [
        'IdInputHarian', 
        'IdIdleTime', 
        'Durasi', 
        'Alasan', 
        'create_by', 
        'update_by'
    ];

    /**
     * Relasi ke Master Idle Time
     */
    public function masterIdle() {
        return $this->belongsTo(\App\Models\Produksi\Master\MsIdleTime::class, 'IdIdleTime', 'IdIdleTime');
    }

    /**
     * Relasi ke Header Input Harian
     */
    public function inputHarian()
    {
        return $this->belongsTo(\App\Models\Produksi\Transaksi\TrsInputHarian::class, 'IdInputHarian', 'IdInputHarian');
    }
}