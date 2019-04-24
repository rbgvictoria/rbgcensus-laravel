<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\Migrations\MigrationTrait;

class CreatePlantAttributesTable extends Migration
{
    use MigrationTrait;
    
    protected $tableName = 'plant_attributes';
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->timestampTz('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestampTz('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->bigInteger('plant_id');
            $table->bigInteger('attribute_id');
            $table->text('value');
            $table->index('plant_id');
            $table->index('attribute_id');
            $table->foreign('plant_id')->references('id')->on('plants');
            $table->foreign('attribute_id')->on('attributes')->references('id');
        });
        
        $this->setGlobalSequence();
        $this->setTriggers();
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
