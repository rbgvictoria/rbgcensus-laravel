<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BedsDropColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('beds', function (Blueprint $table) {
            $table->dropColumn(['precinct_name', 'precinct_code', 
                'subprecinct_name', 'subprecinct_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('beds', function (Blueprint $table) {
            $table->string('precinct_name', 64)->nullable();
            $table->integer('precinct_code')->nullable();
            $table->integer('subprecinct_code')->nullable();
            $table->string('subprecinct_name', 64)->nullable();
        });
    }
}
