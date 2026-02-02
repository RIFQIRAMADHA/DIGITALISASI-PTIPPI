<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
    {
        Schema::rename('ms_itemproduction', 'prod_msItemProduction');
    }

    public function down()
    {
        Schema::rename('prod_msItemProduction', 'ms_itemproduction');
    }
};
