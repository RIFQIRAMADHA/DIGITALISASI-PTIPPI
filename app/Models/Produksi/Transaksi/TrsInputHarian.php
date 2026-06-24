<?php

namespace App\Models\Produksi\Transaksi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
// Import Model Master Baru
use App\Models\Produksi\Master\MsProductionLine;
use App\Models\Produksi\Master\MsItemProduction;
use App\Models\Produksi\Master\MsKaryawan;

// ✅ IMPORT SEMUA MODEL DETAIL YANG UDAH PINDAH RUMAH
use App\Models\Produksi\Detail\DetailPlanScheduleProduksi;
use App\Models\Produksi\Detail\DetailDowntime;
use App\Models\Produksi\Detail\DetailIdleTime;
use App\Models\Produksi\Detail\DetailRepair;
use App\Models\Produksi\Detail\DetailReject;

/**
 * @property string|null $StatusProses
 * @property float|null $RepairA
 * @property float|null $RepairB
 * @property float|null $RejectA
 * @property float|null $RejectB
 * @property float|null $GoodA
 * @property float|null $GoodB
 * @property float|null $TPT
 * @property float|null $IdleTime
 * @method \Illuminate\Database\Eloquent\Relations\BelongsTo productionLine()
 * @method \Illuminate\Database\Eloquent\Relations\BelongsTo item()
 * @method \Illuminate\Database\Eloquent\Relations\BelongsTo karyawan()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany detailsDowntime()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany detailsIdleTime()
 * @method \Illuminate\Database\Eloquent\Relations\HasOne planDetail()
 * @method \Illuminate\Database\Eloquent\Relations\HasOne asakaiMain()
 * @method \Illuminate\Database\Eloquent\Relations\HasOne asakaiSafety()
 * @method \Illuminate\Database\Eloquent\Relations\HasOne asakaiQuality()
 * @method \Illuminate\Database\Eloquent\Relations\HasOne asakaiPencapaian()
 * @method \Illuminate\Database\Eloquent\Relations\HasOne asakaiDowntime()
 * @method \Illuminate\Database\Eloquent\Relations\HasOne asakaiGsph()
 * @method \Illuminate\Database\Eloquent\Relations\HasOne asakaiSpot()
 */

class TrsInputHarian extends Model
{
    // ✅ UPDATE NAMA TABEL
    protected $table = 'prod_trsinputharian';
    
    protected $primaryKey = 'IdInputHarian';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'IdInputHarian', 'IdProductionLine', 'IdItemProduksi', 'NPK', 'NextItemId', 'TanggalProduksi',
        'AktualStart', 'AktualFinish', 'StatusProses', 'TotalProses', 'PlanQtyA', 'PlanQtyB',
        'GoodA', 'GoodB', 'RepairA', 'RepairB', 'RejectA', 'RejectB',
        'AktualQtyA', 'AktualQtyB', 'AktualWorkTime', 'TPT', 'PressTime',
        'LineMonitoring', 'LKHCalculation', 'SotoDandori', 'DiesChange',
        'EarlyCheck', 'TotalUchi', 'TotalDowntime', 'TypeBreakTime', 'TimeBreakTime',
        'PassRate', 'RepairRate', 'RejectRate', 'OEE', 'AktualGSPH', 'Availability', 'QualityRate', 'Performance',
        'QtyMesin1', 'QtyMesin2', 'QtyMesin3', 'QtyMesin4', 'QtyMesin5',
        'create_by', 'update_by', 'PlanGSPH'
    ];

    protected $casts = [
        'TotalProses' => 'decimal:2',
        'AktualWorkTime' => 'decimal:2',
        'TotalDowntime' => 'decimal:2',
        'TimeBreakTime' => 'decimal:2',
        'Availability' => 'decimal:2',
        'QualityRate' => 'decimal:2',
        'Performance' => 'decimal:2',
        'OEE' => 'decimal:2',
        'PassRate' => 'decimal:2',
        'RepairRate' => 'decimal:2',
        'RejectRate' => 'decimal:2',
        'QtyMesin1' => 'decimal:2',
        'QtyMesin2' => 'decimal:2',
        'QtyMesin3' => 'decimal:2',
        'QtyMesin4' => 'decimal:2',
        'QtyMesin5' => 'decimal:2',
    ];

    // ✅ RELASI KE MASTER LINE BARU
    public function productionLine()
    {
        return $this->belongsTo(MsProductionLine::class, 'IdProductionLine', 'IdProductionLine');
    }

    // ✅ RELASI KE MASTER ITEM BARU
    public function item()
    {
        return $this->belongsTo(MsItemProduction::class, 'IdItemProduksi', 'IdItemProduksi');
    }

    // ✅ RELASI KE MASTER KARYAWAN BARU
    public function karyawan()
    {
        return $this->belongsTo(MsKaryawan::class, 'NPK', 'NRPKaryawan');
    }

    public function planDetail(): HasOne 
    {
        $parts = explode('-', (string)$this->IdInputHarian);
        $idPlan = $parts[1] ?? null;

        return $this->hasOne(DetailPlanScheduleProduksi::class, 'IdItemProduksi', 'IdItemProduksi')
                    ->where('IdPlanSchedule', $idPlan);
    }

    public function detailPlan(): HasOne 
    {
        return $this->planDetail();
    }

    // Relasi ke tabel transaksi detail lainnya (Pastikan model ini juga sudah update tabelnya nanti)
    public function detailsDowntime() {
        return $this->hasMany(DetailDowntime::class, 'IdInputHarian', 'IdInputHarian');
    }

    public function detailsIdleTime() {
        return $this->hasMany(DetailIdleTime::class, 'IdInputHarian', 'IdInputHarian');
    }

    public function header()
    {
        return $this->belongsTo(TrsPlanScheduleProduction::class, 'IdPlanSchedule', 'IdPlanSchedule');
    }

    public function getIsEditableAttribute()
    {
        return strtoupper($this->StatusProses) === 'READY' || empty($this->StatusProses);
    }
    

    // Asakai Main pake IdInputHarian (karena di store lo simpan pake IdInputHarian)
    public function asakaiMain() {
        return $this->hasOne(\App\Models\Produksi\Master\MsAsakaiMain::class, 'IdInputHarian', 'IdInputHarian');
    }

    // Sisanya pake TanggalProduksi biar sinkron sama ID Payung (ASA-YYYYMMDD)
    public function asakaiPencapaian() {
        return $this->hasOne(\App\Models\Produksi\Master\MsAsakaiPencapaianProduksi::class, 'TanggalProduksi', 'TanggalProduksi');
    }

    public function asakaiQuality() {
        return $this->hasOne(\App\Models\Produksi\Master\MsAsakaiQuality::class, 'TanggalProduksi', 'TanggalProduksi');
    }

    public function asakaiDowntime() {
        return $this->hasOne(\App\Models\Produksi\Master\MsAsakaiDowntime::class, 'TanggalProduksi', 'TanggalProduksi');
    }

    public function asakaiGsph() {
        return $this->hasOne(\App\Models\Produksi\Master\MsAsakaiGsph::class, 'TanggalProduksi', 'TanggalProduksi');
    }

    public function asakaiSafety() {
        return $this->hasOne(\App\Models\Produksi\Master\MsAsakaiSafety::class, 'TanggalProduksi', 'TanggalProduksi');
    }

    public function asakaiSpot() {
        return $this->hasOne(\App\Models\Produksi\Master\MsAsakaiSpot::class, 'TanggalProduksi', 'TanggalProduksi');
    }
}