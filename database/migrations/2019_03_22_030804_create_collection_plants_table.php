<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\Migrations\MigrationTrait;

class CreateCollectionPlantsTable extends Migration
{
    use MigrationTrait;
    
    protected $tableName = 'collection_plants';
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigInteger('plant_id');
            $table->bigInteger('collection_id');
            $table->index('plant_id');
            $table->index('collection_id');
            $table->foreign('plant_id')->on('plants')->references('id');
            $table->foreign('collection_id')->on('collections')->references('id');
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
