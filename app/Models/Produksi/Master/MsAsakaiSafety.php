<?php

namespace App\Models\Produksi\Master;

use Illuminate\Database\Eloquent\Model;

class MsAsakaiSafety extends Model
{
    // Nama tabel sesuai screenshot database
    protected $table = 'prod_msasakaisafety';
    
    // Primary Key
    protected $primaryKey = 'IdAsakaiSafety';
    
    // Nonaktifkan auto-increment karena PK adalah varchar(255)
    public $incrementing = false;
    protected $keyType = 'string';

    // Set false karena tidak ada kolom created_at & updated_at di screenshot
    public $timestamps = true;

    protected $fillable = [
        'IdAsakaiSafety',
        'TanggalProduksi',
        
        'AccidentTarget', 'AccidentAct', 'AccidentAccum', 'AccidentIssue', 'AccidentPIC',
        'InccidentTarget', 'InccidentAct', 'InccidentAccum', 'InccidentIssue', 'InccidentPIC',
        'TrafficTarget', 'TrafficAct', 'TrafficAccum', 'TrafficIssue', 'TrafficPIC', 'SafetyPic'
    ];

    protected $casts = [
        'AccidentTarget'  => 'decimal:2', 'AccidentAct' => 'decimal:2', 'AccidentAccum' => 'decimal:2',
        'InccidentTarget' => 'decimal:2', 'InccidentAct' => 'decimal:2', 'InccidentAccum' => 'decimal:2',
        'TrafficTarget'   => 'decimal:2', 'TrafficAct'  => 'decimal:2', 'TrafficAccum'  => 'decimal:2',
    ];

    /**
     * Relasi balik ke Master Transaksi Harian
     * (Asumsi: IdSafety diisi dengan IdInputHarian atau 
     * menggunakan prefix 'SAFE-' . IdInputHarian)
     */
    public function inputHarian()
    {
        return $this->belongsTo(\App\Models\Produksi\Transaksi\TrsInputHarian::class, 'IdSafety', 'IdInputHarian');
    }
}