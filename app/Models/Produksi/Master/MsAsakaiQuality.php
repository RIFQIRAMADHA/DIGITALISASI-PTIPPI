<?php

namespace App\Models\Produksi\Master;
use Illuminate\Database\Eloquent\Model;

class MsAsakaiQuality extends Model
{
    protected $table = 'prod_msasakaiquality';
    protected $primaryKey = 'IdAsakaiQuality';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'IdAsakaiQuality', 
        'IdInputHarian',
        'TanggalProduksi',

        'CustomersTarget', 'CustomersAct', 'CustomersAcc', 'CustomersIssue', 'CustomersPIC',
        'InternalTarget', 'InternalAct', 'InternalAcc', 'InternalIssue', 'InternalPIC',
        'SupplierTarget', 'SupplierAct', 'SupplierAcc', 'SupplierIssue', 'SupplierPIC',
        
        // Repair Section
        'RepairLineEAct', 'RepairLineEAcc', 'RepairIssueLineE',
        'RepairLineFAct', 'RepairLineFAcc', 'RepairIssueLineF',
        'RepairLineKAct', 'RepairLineKAcc', 'RepairIssueLineK',
        'RepairSubAssyAct', 'RepairSubAssyAcc', 'RepairIssueSubAssy',
        
        // Reject Section
        'RejectLineEAct', 'RejectLineEAcc', 'RejectIssueLineE',
        'RejectLineFAct', 'RejectLineFAcc', 'RejectIssueLineF',
        'RejectLineKAct', 'RejectLineKAcc', 'RejectIssueLineK',
        'RejectSubAssyAct', 'RejectSubAssyAcc', 'RejectIssueSubAssy',

        'RepairPIC', 'RejectPIC', 'QualPic'
    ];

    protected $casts = [
        // Flowout Section
        'CustomersTarget' => 'decimal:2', 'CustomersAct' => 'decimal:2', 'CustomersAcc' => 'decimal:2',
        'InternalTarget'  => 'decimal:2', 'InternalAct'  => 'decimal:2', 'InternalAcc'  => 'decimal:2',
        'SupplierTarget'  => 'decimal:2', 'SupplierAct'  => 'decimal:2', 'SupplierAcc'  => 'decimal:2',

        // Repair Section
        'RepairLineEAct'   => 'decimal:2', 'RepairLineEAcc'   => 'decimal:2',
        'RepairLineFAct'   => 'decimal:2', 'RepairLineFAcc'   => 'decimal:2',
        'RepairLineKAct'   => 'decimal:2', 'RepairLineKAcc'   => 'decimal:2',
        'RepairSubAssyAct' => 'decimal:2', 'RepairSubAssyAcc' => 'decimal:2',

        // Reject Section
        'RejectLineEAct'   => 'decimal:2', 'RejectLineEAcc'   => 'decimal:2',
        'RejectLineFAct'   => 'decimal:2', 'RejectLineFAcc'   => 'decimal:2',
        'RejectLineKAct'   => 'decimal:2', 'RejectLineKAcc'   => 'decimal:2',
        'RejectSubAssyAct' => 'decimal:2', 'RejectSubAssyAcc' => 'decimal:2',
    ];

    public function inputHarian()
    {
        return $this->belongsTo(\App\Models\Produksi\Transaksi\TrsInputHarian::class, 'IdInputHarian', 'IdInputHarian');
    }
}