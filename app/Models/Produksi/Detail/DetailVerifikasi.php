<?php

namespace App\Models\Produksi\Detail;

use Illuminate\Database\Eloquent\Model;

class DetailVerifikasi extends Model
{
    protected $table = 'prod_detailverifikasi';
    protected $primaryKey = 'IdVerifikasi';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IdQpr', 'IdVerifikasi', 'LangkahPerbaikan', 'Schedule', 
        'TanggalVerifikasi', 'MethodeCheck1','MethodeCheck2','MethodeCheck3', 'Status'
    ];

    public function header()
    {
        return $this->belongsTo(\App\Models\Produksi\Transaksi\TrsQpr::class, 'IdQpr', 'IdQpr');
    }
}