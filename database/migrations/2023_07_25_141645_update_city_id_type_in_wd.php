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
            // Remove the existing column
            $table->dropColumn('city_id');

        });

        // Add the foreign key constraint back
        Schema::table('wd', function (Blueprint $table) {
            $table->foreignUuid('city_id')->nullable()->after('firm_name')->constrained()->nullOnDelete();
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
            // Drop the existing foreign key constraint if it exists
            $table->dropForeign(['city_id']);
            $table->dropColumn('city_id');

            // Change the column type back to string
            // $table->string('city_id')->constrained()->nullOnDelete()->nullable()->change();
        });
    }
};
