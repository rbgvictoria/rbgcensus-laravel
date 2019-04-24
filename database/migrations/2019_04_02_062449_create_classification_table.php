<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classification', function (Blueprint $table) {
            $table->bigInteger('taxon_id');
            $table->string('kingdom', 32)->nullable();
            $table->string('phylum', 32)->nullable();
            $table->string('class', 32)->nullable();
            $table->string('order', 32)->nullable();
            $table->string('family', 32)->nullable();
            $table->string('genus', 32)->nullable();
            $table->string('species', 64)->nullable();
            $table->string('subspecies', 64)->nullable();
            $table->string('variety', 64)->nullable();
            $table->string('form', 64)->nullable();
            $table->string('cultivar', 64)->nullable();
            $table->index('taxon_id');
            $table->index('kingdom');
            $table->index('phylum');
            $table->index('class');
            $table->index('order');
            $table->index('family');
            $table->index('genus');
            $table->index('species');
            $table->foreign('taxon_id')->on('taxa')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classification');
    }
}
