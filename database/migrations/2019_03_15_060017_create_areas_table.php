<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\Migrations\MigrationTrait;

class CreateAreasTable extends Migration
{
    
    use MigrationTrait;
    
    protected $tableName = 'areas';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestampTz('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestampTz('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->bigInteger('parent_id')->nullable();
            $table->string('locality_id', 16);
            $table->string('area_name', 64);
            $table->string('area_full_name');
            $table->string('iso_code', 4)->nullable();
            $table->integer('node_number')->nullable();
            $table->integer('highest_descendant_node_number')->nullable();
            $table->smallInteger('depth')->nullable();
            $table->multipolygon('geom', 'GEOMETRY', 4326)->nullable();
            $table->index('parent_id');
            $table->index('area_name');
            $table->index('area_full_name');
            $table->index('locality_id');
            $table->spatialIndex('geom');
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
