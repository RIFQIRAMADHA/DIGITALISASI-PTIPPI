<?php

namespace App\Models\Produksi\Master;

use Illuminate\Database\Eloquent\Model;

class MsAsakaiDowntime extends Model
{
    protected $table = 'prod_msasakaidowntime';
    protected $primaryKey = 'IdAsakaiDowntime';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'IdAsakaiDowntime', 
        'IdInputHarian',
        'TanggalProduksi',
        
        // --- SHIFT 1 ---
        // Line-specific (E, F, K)
        'LineETodayDTS1', 'LineEAccDTS1', 'LineETipeDTS1', 'LineEIssueDTS1',
        'LineFTodayDTS1', 'LineFAccDTS1', 'LineFTipeDTS1', 'LineFIssueDTS1',
        'LineKTodayDTS1', 'LineKAccDTS1', 'LineKTipeDTS1', 'LineKIssueDTS1',
        // Welding & Handwork
        'D52VtTodayDTS1', 'D52VtAccDTS1', 'D52VtTipeDTS1', 'D52VtIssueDTS1',
        'D26TodayDTS1',   'D26AccDTS1',   'D26TipeDTS1',   'D26IssueDTS1',
        'HandworkTodayDTS1', 'HandworkAccDTS1', 'HandworkTipeDTS1', 'HandworkIssueDTS1',

        // --- SHIFT 2 ---
        'LineETodayDTS2', 'LineEAccDTS2', 'LineETipeDTS2', 'LineEIssueDTS2',
        'LineFTodayDTS2', 'LineFAccDTS2', 'LineFTipeDTS2', 'LineFIssueDTS2',
        'LineKTodayDTS2', 'LineKAccDTS2', 'LineKTipeDTS2', 'LineKIssueDTS2',
        'D52VtTodayDTS2', 'D52VtAccDTS2', 'D52VtTipeDTS2', 'D52VtIssueDTS2',
        'D26TodayDTS2',   'D26AccDTS2',   'D26TipeDTS2',   'D26IssueDTS2',
        'HandworkTodayDTS2', 'HandworkAccDTS2', 'HandworkTipeDTS2', 'HandworkIssueDTS2',

        'DtPicS1', 'DtPicS2'
    ];

    protected $casts = [
        // Shift 1
        'LineETodayDTS1' => 'decimal:2', 'LineEAccDTS1' => 'decimal:2',
        'LineFTodayDTS1' => 'decimal:2', 'LineFAccDTS1' => 'decimal:2',
        'LineKTodayDTS1' => 'decimal:2', 'LineKAccDTS1' => 'decimal:2',
        'D52VtTodayDTS1' => 'decimal:2', 'D52VtAccDTS1' => 'decimal:2',
        'D26TodayDTS1'   => 'decimal:2', 'D26AccDTS1'   => 'decimal:2',
        'HandworkTodayDTS1' => 'decimal:2', 'HandworkAccDTS1' => 'decimal:2',

        // Shift 2
        'LineETodayDTS2' => 'decimal:2', 'LineEAccDTS2' => 'decimal:2',
        'LineFTodayDTS2' => 'decimal:2', 'LineFAccDTS2' => 'decimal:2',
        'LineKTodayDTS2' => 'decimal:2', 'LineKAccDTS2' => 'decimal:2',
        'D52VtTodayDTS2' => 'decimal:2', 'D52VtAccDTS2' => 'decimal:2',
        'D26TodayDTS2'   => 'decimal:2', 'D26AccDTS2'   => 'decimal:2',
        'HandworkTodayDTS2' => 'decimal:2', 'HandworkAccDTS2' => 'decimal:2',
    ];

    public function inputHarian()
    {
        return $this->belongsTo(\App\Models\Produksi\Transaksi\TrsInputHarian::class, 'IdInputHarian', 'IdInputHarian');
    }
}