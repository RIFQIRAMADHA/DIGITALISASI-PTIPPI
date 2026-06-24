<?php

namespace App\Models\Produksi\Master;

use Illuminate\Database\Eloquent\Model;

class MsIdleTime extends Model
{
    // Nama tabel sesuai di database
    protected $table = 'prod_msidletime';

    // Primary Key sesuai ERD
    protected $primaryKey = 'IdIdleTime';

    // Karena IdIdleTime adalah string (VARCHAR), matikan auto-increment
    public $incrementing = false;
    protected $keyType = 'string';

    // Kolom yang boleh diisi
    protected $fillable = ['IdIdleTime', 'TipeIdleTime', 'Status'];
}