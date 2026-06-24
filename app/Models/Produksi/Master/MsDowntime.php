<?php

namespace App\Models\Produksi\Master;

use Illuminate\Database\Eloquent\Model;

class MsDowntime extends Model
{
    protected $table = 'prod_msdowntime';
    
    protected $primaryKey = 'IdDowntime';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'IdDowntime',
        'TipeDowntime',
        'Status'
    ];

    public $timestamps = false;

    /**
     * Relasi ke Tabel Detail
     * Karena logic baru kita menyimpan ID Master di kolom 'Keterangan'
     */
    public function details()
    {
        return $this->hasMany(\App\Models\Produksi\Detail\DetailDowntime::class, 'Keterangan', 'IdDowntime');
    }
}