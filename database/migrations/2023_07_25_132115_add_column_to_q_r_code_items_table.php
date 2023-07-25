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
        Schema::table('q_r_code_items', function (Blueprint $table) {
            $table->boolean('is_qr_code_generated')->default(0)->after('is_redeemed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('q_r_code_items', function (Blueprint $table) {
            $table->dropColumn('is_qr_code_generated');
        });
    }
};
