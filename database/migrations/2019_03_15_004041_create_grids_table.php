<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\Migrations\MigrationTrait;

class CreateGridsTable extends Migration
{
    
    use MigrationTrait;
    
    protected $tableName = 'grids';
    
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
            $table->string('code', 6);
            $table->multipolygon('geom', 'GEOMETRY', 900913)->nullable();
            $table->multipolygon('geom_mga', 'GEOMETRY', 28355)->nullable();
            $table->index('code');
            $table->spatialIndex('geom');
            $table->spatialIndex('geom_mga');
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
        Schema::dropIfExists('grids');
    }
}
