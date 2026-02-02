<?php

namespace App\Models\Produksi\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionLine extends Model
{
    use HasFactory;

    protected $table = 'prod_msProductionLine';
    protected $primaryKey = 'IdProductionLine';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'IdProductionLine',
        'NamaProductionLine',
        'Shift',
        'Status',
        'create_by',
        'update_by'
    ];

}