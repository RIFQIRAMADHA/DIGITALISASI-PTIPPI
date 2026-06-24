<?php

namespace App\Models\Produksi\Master;

use Illuminate\Database\Eloquent\Model;

class MsVerifikasi extends Model
{
    protected $table = 'prod_msverifikasi';
    protected $primaryKey = 'IdVerifikasi';
    public $incrementing = false; // Sesuai skema VARCHAR
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['IdVerifikasi', 'Verifikasi'];
}