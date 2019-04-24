<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\Migrations\MigrationTrait;

class CreatePlantsTable extends Migration
{
    
    use MigrationTrait;
    
    protected $tableName = 'plants';
    
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
            $table->bigInteger('accession_id');
            $table->bigInteger('grid_id')->nullable();
            $table->bigInteger('bed_id')->nullable();
            $table->integer('plant_number');
            $table->date('date_planted')->nullable();
            $table->index('accession_id');
            $table->index('grid_id');
            $table->index('bed_id');
            $table->unique(['accession_id', 'plant_number']);
            $table->foreign('accession_id')->references('id')->on('accessions');
            $table->foreign('grid_id')->references('id')->on('grids');
            $table->foreign('bed_id')->references('id')->on('beds');
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
