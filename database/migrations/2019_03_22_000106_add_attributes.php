<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAttributes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('attributes')->insert(['name' => 'commemorative']);
        DB::table('attributes')->insert(['name' => 'inMemoryOf']);
        DB::table('attributes')->insert(['name' => 'datePlanted']);
        DB::table('attributes')->insert(['name' => 'nationalTrustStatus']);
        DB::table('attributes')->insert([
            'name' => 'nationalTrustSignificance',
            'db_column_name' => 'National_Trust_Significance',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('attributes')->delete();
    }
}
