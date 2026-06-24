<?php

namespace App\Models\Produksi\Master;

use Illuminate\Database\Eloquent\Model;

class MsMasalah extends Model
{
    protected $table = 'prod_msmasalah';
    protected $primaryKey = 'IdMasalah';
    public $incrementing = false; // Karena PK Lu string
    protected $keyType = 'string';
    public $timestamps = false; // Tabel master biasanya statis

    protected $fillable = ['IdMasalah', 'Problem'];
}