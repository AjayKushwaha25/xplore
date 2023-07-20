<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wd', function (Blueprint $table) {
            $table->string('city_id')->constrained()->nullOnDelete()->nullable()->after('firm_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wd', function (Blueprint $table) {
            //
            $table->dropColumn('city_id');
        });
    }
};
