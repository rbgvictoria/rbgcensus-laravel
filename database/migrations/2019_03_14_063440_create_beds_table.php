<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Database\Migrations\MigrationTrait;

class CreateBedsTable extends Migration
{
    
    use MigrationTrait;
    
    protected $tableName = 'beds';
    
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
            $table->bigInteger('bed_type_id');
            $table->string('bed_name', 64);
            $table->string('bed_full_name')->nullable();
            $table->string('bed_code', 4)->nullable();
            $table->string('precinct_name', 64)->nullable();
            $table->boolean('is_restricted')->nullable();
            $table->string('location', 16);
            $table->integer('precinct_code')->nullable();
            $table->integer('subprecinct_code')->nullable();
            $table->string('subprecinct_name', 64)->nullable();
            $table->integer('node_number')->nullable();
            $table->integer('highest_descendant_node_number')->nullable();
            $table->smallInteger('depth')->nullable();
            $table->index('parent_id');
            $table->index('bed_type_id');
            $table->index('bed_name');
            $table->index('bed_full_name');
            $table->index('bed_code');
            $table->foreign('parent_id')->on('beds')->references('id');
            $table->foreign('bed_type_id')->on('bed_types')->references('id');
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
