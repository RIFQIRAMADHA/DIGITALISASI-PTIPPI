<?php

namespace App\Models\Produksi\Detail;

use Illuminate\Database\Eloquent\Model;

class DetailMasalah extends Model
{
    protected $table = 'prod_detailmasalah';
    protected $primaryKey = 'IdMasalah';
    public $incrementing = false;
    public $timestamps = false; // Biasanya detail tabel gak pake timestamps

    protected $fillable = [
        'IdQpr', 'IdMasalah', 'NomorKerusakan', 'Keterangan', 
        'DeskripsiProblem', 'LastDateProblem', 'AnalisaPenyebab', 
        'Correction', 'TargetCorrection', 'PICCorrection', 'StatusCorrection',
        'Correction2', 'TargetCorrection2', 'PICCorrection2', 'StatusCorrection2'
    ];

    public function header()
    {
        return $this->belongsTo(\App\Models\Produksi\Transaksi\TrsQpr::class, 'IdQpr', 'IdQpr');
    }
}