<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\Migrations\MigrationTrait;

class CreateAccessionsTable extends Migration
{
    
    use MigrationTrait;
    
    protected $tableName = 'accessions';
    
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
            $table->bigInteger('provenance_type_id')->nullable();
            $table->bigInteger('identification_status_id')->nullable();
            $table->string('accession_number', 16);
            $table->string('collector_name', 64)->nullable();
            $table->bigInteger('taxon_id');
            $table->index('taxon_id');
            $table->index('accession_number');
            $table->index('provenance_type_id');
            $table->index('identification_status_id');
            $table->foreign('taxon_id')->on('taxa')->references('id');
            $table->foreign('provenance_type_id')->on('provenance_types')->references('id');
            $table->foreign('identification_status_id')->on('identification_statuses')->references('id');
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
