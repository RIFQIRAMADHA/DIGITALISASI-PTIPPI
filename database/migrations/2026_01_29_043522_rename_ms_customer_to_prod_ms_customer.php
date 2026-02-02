<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::rename('ms_customer', 'prod_msCustomer');
    }

    public function down()
    {
        Schema::rename('prod_msCustomer', 'ms_customer');
    }
};
