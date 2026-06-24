<?php

namespace App\Models\Produksi\Master;

use Illuminate\Database\Eloquent\Model;

class MsReject extends Model
{
    protected $table = 'prod_msreject';
    protected $primaryKey = 'IdReject';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['IdReject', 'TipeReject', 'Status'];
}