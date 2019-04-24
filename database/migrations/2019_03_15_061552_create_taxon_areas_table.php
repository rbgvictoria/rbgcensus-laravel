<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\Migrations\MigrationTrait;

class CreateTaxonAreasTable extends Migration
{
    use MigrationTrait;
    
    protected $tableName = 'taxon_areas';
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigInteger('taxon_id');
            $table->bigInteger('area_id');
            $table->index('taxon_id');
            $table->index('area_id');
            $table->foreign('taxon_id')->on('taxa')->references('id');
            $table->foreign('area_id')->on('areas')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
}
