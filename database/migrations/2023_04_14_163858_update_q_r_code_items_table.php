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
            $table->string('key')->collation('utf8mb4_bin')->change();
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
            $table->string('key')->collation('utf8mb4_unicode_ci')->change();
        });
    }
};
