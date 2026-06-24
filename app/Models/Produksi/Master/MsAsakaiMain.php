<?php

namespace App\Models\Produksi\Master;

use Illuminate\Database\Eloquent\Model;

class MsAsakaiMain extends Model
{
    // Nama tabel sesuai screenshot database kamu
    protected $table = 'prod_msasakai';
    
    // Primary Key string sesuai migrasi kita sebelumnya
    protected $primaryKey = 'IdAsakai';
    
    // Nonaktifkan auto-increment karena kita pakai string PK
    public $incrementing = false;
    protected $keyType = 'string';

    // Matikan timestamps jika tabel database-mu tidak memilikinya
    public $timestamps = false;

    protected $fillable = [
        'IdAsakai', 
        'IdInputHarian',
        'TanggalProduksi',
        
        // --- SHIFT 1 ---
        'PlanGlcLES1', 'PlanTptLES1', 'CapRegLES1', 'RemarksLES1',
        'PlanGlcLFS1', 'PlanTptLFS1', 'CapRegLFS1', 'RemarksLFS1',
        'PlanGlcLKS1', 'PlanTptLKS1', 'CapRegLKS1', 'RemarksLKS1',
        'PlanGlcD52S1', 'PlanTptD52S1', 'CapRegD52S1', 'RemarksD52S1',
        'PlanGlcD26S1', 'PlanTptD26S1', 'CapRegD26S1', 'RemarksD26S1',
        'PlanGlcMetalS1', 'PlanTptMetalS1', 'CapRegMetalS1', 'RemarksMetalS1',
        
        // --- SHIFT 2 ---
        'PlanGlcLES2', 'PlanTptLES2', 'CapRegLES2', 'RemarksLES2',
        'PlanGlcLFS2', 'PlanTptLFS2', 'CapRegLFS2', 'RemarksLFS2',
        'PlanGlcLKS2', 'PlanTptLKS2', 'CapRegLKS2', 'RemarksLKS2',
        'PlanGlcD52S2', 'PlanTptD52S2', 'CapRegD52S2', 'RemarksD52S2',
        'PlanGlcD26S2', 'PlanTptD26S2', 'CapRegD26S2', 'RemarksD26S2',
        'PlanGlcMetalS2', 'PlanTptMetalS2', 'CapRegMetalS2', 'RemarksMetalS2'
    ];

    /**
     * Konversi otomatis tipe data saat dipanggil di Controller/View
     */
    protected $casts = [
        // Shift 1 Casts
        'PlanGlcLES1' => 'decimal:2', 'PlanTptLES1' => 'decimal:2', 'CapRegLES1' => 'decimal:2',
        'PlanGlcLFS1' => 'decimal:2', 'PlanTptLFS1' => 'decimal:2', 'CapRegLFS1' => 'decimal:2',
        'PlanGlcLKS1' => 'decimal:2', 'PlanTptLKS1' => 'decimal:2', 'CapRegLKS1' => 'decimal:2',
        'PlanGlcD52S1' => 'decimal:2', 'PlanTptD52S1' => 'decimal:2', 'CapRegD52S1' => 'decimal:2',
        'PlanGlcD26S1' => 'decimal:2', 'PlanTptD26S1' => 'decimal:2', 'CapRegD26S1' => 'decimal:2',
        'PlanGlcMetalS1' => 'decimal:2', 'PlanTptMetalS1' => 'decimal:2', 'CapRegMetalS1' => 'decimal:2',
        
        // Shift 2 Casts
        'PlanGlcLES2' => 'decimal:2', 'PlanTptLES2' => 'decimal:2', 'CapRegLES2' => 'decimal:2',
        'PlanGlcLFS2' => 'decimal:2', 'PlanTptLFS2' => 'decimal:2', 'CapRegLFS2' => 'decimal:2',
        'PlanGlcLKS2' => 'decimal:2', 'PlanTptLKS2' => 'decimal:2', 'CapRegLKS2' => 'decimal:2',
        'PlanGlcD52S2' => 'decimal:2', 'PlanTptD52S2' => 'decimal:2', 'CapRegD52S2' => 'decimal:2',
        'PlanGlcD26S2' => 'decimal:2', 'PlanTptD26S2' => 'decimal:2', 'CapRegD26S2' => 'decimal:2',
        'PlanGlcMetalS2' => 'decimal:2', 'PlanTptMetalS2' => 'decimal:2', 'CapRegMetalS2' => 'decimal:2',
    ];

    /**
     * Relasi balik ke Transaksi Input Harian
     */
    public function inputHarian()
    {
        return $this->belongsTo(\App\Models\Produksi\Transaksi\TrsInputHarian::class, 'IdInputHarian', 'IdInputHarian');
    }
}