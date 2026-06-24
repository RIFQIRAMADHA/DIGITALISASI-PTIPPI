<?php

namespace App\Models\Produksi\Transaksi;

use Illuminate\Database\Eloquent\Model;
use App\Models\Produksi\Transaksi\TrsInputHarian;
use App\Models\Produksi\Detail\DetailMasalah;
use App\Models\Produksi\Detail\DetailVerifikasi;

/**
 * @method \Illuminate\Database\Eloquent\Relations\BelongsTo inputHarian()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany detailsMasalah()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany detailsVerifikasi()
 */

class TrsQpr extends Model
{
    protected $table = 'prod_trsqpr';
    protected $primaryKey = 'IdQpr';
    public $incrementing = false; // Karena PK Lu string
    protected $keyType = 'string';

    protected $fillable = [
        'IdQpr', 'IdInputHarian', 'Rework', 'Reject', 'Stok', 
        'RencanaProduksi', 'ProsesRepair', 'LokasiKejadian', 
        'DocReferensi', 'Jam', 'create_by', 'update_by'
    ];

    // Relasi ke Input Harian
    public function inputHarian()
    {
        return $this->belongsTo(TrsInputHarian::class, 'IdInputHarian', 'IdInputHarian');
    }

    // Relasi ke Detail Masalah (One to Many)
    public function detailsMasalah()
    {
        return $this->hasMany(DetailMasalah::class, 'IdQpr', 'IdQpr');
    }

    // Relasi ke Detail Verifikasi (One to Many)
    public function detailsVerifikasi()
    {
        return $this->hasMany(DetailVerifikasi::class, 'IdQpr', 'IdQpr');
    }
}