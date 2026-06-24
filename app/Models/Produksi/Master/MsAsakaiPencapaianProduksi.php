<?php

namespace App\Models\Produksi\Master;

use Illuminate\Database\Eloquent\Model;

class MsAsakaiPencapaianProduksi extends Model
{
    protected $table = 'prod_msasakaipencapaianproduksi';
    protected $primaryKey = 'IdAsakaiPP';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // Matikan jika tabel tidak punya created_at/updated_at

    protected $fillable = [
        'IdAsakaiPP', 
        'IdInputHarian',
        'TanggalProduksi',
        
        // --- SHIFT 1 ---
        'LineEPlanS1', 'LineEActS1', 'LineEIssueS1',
        'LineFPlanS1', 'LineFActS1', 'LineFIssueS1',
        'LineKPlanS1', 'LineKActS1', 'LineKIssueS1',
        'D52VtPlanS1', 'D52VtActS1', 'D52VtIssueS1',
        'D26PlanS1', 'D26ActS1', 'D26Issue_S1', // Ada underscore sesuai DB lo
        'HandworkPlanS1', 'HandworkActS1', 'HandworkIssueS1',

        // --- SHIFT 2 ---
        'LineEPlanS2', 'LineEActS2', 'LineEIssueS2',
        'LineFPlanS2', 'LineFActS2', 'LineFIssueS2',
        'LineKPlanS2', 'LineKActS2', 'LineKIssueS2',
        'D52VtPlanS2', 'D52VtActS2', 'D52VtIssueS2',
        'D26PlanS2', 'D26ActS2', 'D26Issue_S2',
        'HandworkPlanS2', 'HandworkActS2', 'HandworkIssueS2',

        'ProdPicS1', 'ProdPicS2'
    ];

    protected $casts = [
        // Shift 1
        'LineEPlanS1' => 'decimal:2', 'LineEActS1' => 'decimal:2',
        'LineFPlanS1' => 'decimal:2', 'LineFActS1' => 'decimal:2',
        'LineKPlanS1' => 'decimal:2', 'LineKActS1' => 'decimal:2',
        'D52VtPlanS1' => 'decimal:2', 'D52VtActS1' => 'decimal:2',
        'D26PlanS1'   => 'decimal:2', 'D26ActS1'   => 'decimal:2',
        'HandworkPlanS1' => 'decimal:2', 'HandworkActS1' => 'decimal:2',

        // Shift 2
        'LineEPlanS2' => 'decimal:2', 'LineEActS2' => 'decimal:2',
        'LineFPlanS2' => 'decimal:2', 'LineFActS2' => 'decimal:2',
        'LineKPlanS2' => 'decimal:2', 'LineKActS2' => 'decimal:2',
        'D52VtPlanS2' => 'decimal:2', 'D52VtActS2' => 'decimal:2',
        'D26PlanS2'   => 'decimal:2', 'D26ActS2'   => 'decimal:2',
        'HandworkPlanS2' => 'decimal:2', 'HandworkActS2' => 'decimal:2',
    ];

    public function inputHarian()
    {
        return $this->belongsTo(\App\Models\Produksi\Transaksi\TrsInputHarian::class, 'IdInputHarian', 'IdInputHarian');
    }
}