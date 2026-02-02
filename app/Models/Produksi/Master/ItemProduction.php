<?php

namespace App\Models\Produksi\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemProduction extends Model
{
    use HasFactory;

    protected $table = 'prod_msItemProduction'; // sesuai validasi kamu
    protected $primaryKey = 'IdItemProduksi';
    protected $keyType = 'string';
    public $incrementing = false; // karena pakai id manual

    //karena di migration ada timestamps
    public $timestamps = true;
    

    protected $fillable = [
        'IdItemProduksi',
        'IdCustomer',
        'JobNumber',
        'PartNumber',
        'NamaPart',
        'Model',
        'Gambar',
        'Status',
        'create_by',
        'update_by'
    ];

    public function customer()
    {
        return $this -> belongsTo(Customer::class,'IdCustomer', 'IdCustomer');
    }
}
