<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateRbgcensusGlobalSeq extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $dropGlobalSequence = "DROP SEQUENCE IF EXISTS rbgcensus_global_seq";
        DB::unprepared($dropGlobalSequence);
        $createGlobalSequence = "CREATE SEQUENCE rbgcensus_global_seq";
        DB::unprepared($createGlobalSequence);
        
        $currentTimestampFunction = <<<EOT
CREATE OR REPLACE FUNCTION update_updated_at()
RETURNS TRIGGER AS $$
BEGIN
   NEW.updated_at = now(); 
   RETURN NEW;
END;
$$ language 'plpgsql';
EOT;
        DB::unprepared($currentTimestampFunction);
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $dropGlobalSequence = "DROP SEQUENCE IF EXISTS rbgcensus_global_seq";
        DB::unprepared($dropGlobalSequence);
        
        DB::unprepared('DROP FUNCTION IF EXISTS update_aupdated_at');
    }
}
