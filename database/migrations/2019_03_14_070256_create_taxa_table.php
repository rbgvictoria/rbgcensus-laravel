<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\Migrations\MigrationTrait;

class CreateTaxaTable extends Migration
{
    
    use MigrationTrait;
    
    protected $tableName = 'taxa';
    
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
            $table->bigInteger('rank_id');
            $table->bigInteger('parent_id')->nullable();
            $table->bigInteger('accepted_id')->nullable();
            $table->bigInteger('plant_type_id')->nullable();
            $table->string('taxon_name', 128);
            $table->string('author', 128)->nullable();
            $table->string('vernacular_name', 64)->nullable();
            $table->boolean('hide_from_public_display')->nullable();
            $table->integer('lcd_species_id')->nullable();
            $table->integer('node_number')->nullable();
            $table->integer('highest_descendant_node_number')->nullable();
            $table->smallInteger('depth')->nullable();
            $table->index('rank_id');
            $table->index('parent_id');
            $table->index('accepted_id');
            $table->index('plant_type_id');
            $table->index('taxon_name');
            $table->index('hide_from_public_display');
            $table->index('lcd_species_id');
            $table->foreign('rank_id')->on('ranks')->references('id');
            $table->foreign('parent_id')->on('taxa')->references('id');
            $table->foreign('accepted_id')->on('taxa')->references('id');
            $table->foreign('plant_type_id')->on('plant_types')->references('id');
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
