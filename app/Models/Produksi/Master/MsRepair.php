<?php

// Sesuaikan folder namespace agar terbaca oleh Controller
namespace App\Models\Produksi\Master; 

use Illuminate\Database\Eloquent\Model;

class MsRepair extends Model // Pakai MsRepair (Sesuai pemanggilan di Controller)
{
    protected $table = 'prod_msrepair'; // Bener
    protected $primaryKey = 'IdRepair'; // Bener
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['IdRepair', 'TipeRepair', 'Status'];
}