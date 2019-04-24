<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PopulateConfigTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        /*
         * Bed types
         */
        DB::table('bed_types')->insert([
            'name' => 'location',
            'abbreviation' => 'location'
        ]);
        
        DB::table('bed_types')->insert([
            'name' => 'section',
            'abbreviation' => 'section'
        ]);
        
        DB::table('bed_types')->insert([
            'name' => 'bed',
            'abbreviation' => 'bed'
        ]);
        
        DB::table('bed_types')->insert([
            'name' => 'precinct',
            'abbreviation' => 'precinct'
        ]);
        
        DB::table('bed_types')->insert([
            'name' => 'subprecinct',
            'abbreviation' => 'subprecinct'
        ]);
        
        /*
         * Ranks
         */
        $kingdomId = DB::table('ranks')->insertGetId([
            'name' => 'kingdom',
            'sort_order' => 10
        ]);
        
        $phylumId = DB::table('ranks')->insertGetId([
            'parent_id' => $kingdomId,
            'name' => 'phylum',
            'sort_order' => 30,
        ]);

        $classId = DB::table('ranks')->insertGetId([
            'parent_id' => $phylumId,
            'name' => 'class',
            'sort_order' => 60,
        ]);

        $orderId = DB::table('ranks')->insertGetId([
            'parent_id' => $classId,
            'name' => 'order',
            'sort_order' => 100,
        ]);

        $familyId = DB::table('ranks')->insertGetId([
            'parent_id' => $orderId,
            'name' => 'family',
            'sort_order' => 140,
        ]);

        $genusId = DB::table('ranks')->insertGetId([
            'parent_id' => $familyId,
            'name' => 'genus',
            'sort_order' => 180,
        ]);

        $speciesId = DB::table('ranks')->insertGetId([
            'parent_id' => $genusId,
            'name' => 'species',
            'sort_order' => 220,
        ]);

        DB::table('ranks')->insertGetId([
            'parent_id' => $speciesId,
            'name' => 'subspecies',
            'sort_order' => 230,
        ]);

        DB::table('ranks')->insertGetId([
            'parent_id' => $speciesId,
            'name' => 'variety',
            'sort_order' => 240,
        ]);

        DB::table('ranks')->insertGetId([
            'parent_id' => $speciesId,
            'name' => 'form',
            'sort_order' => 260,
        ]);
        
        DB::table('ranks')->insertGetId([
            'parent_id' => null,
            'name' => 'cultivar',
            'sort_order' => 1000,
        ]);
        
        /*
         * Plant types
         */
        DB::table('plant_types')->insert([
            'name' => '!',
            'abbreviation' => '!'
        ]);
        
        DB::table('plant_types')->insert([
            'name' => '&',
            'abbreviation' => '&'
        ]);
        
        DB::table('plant_types')->insert([
            'name' => '#',
            'abbreviation' => '#'
        ]);
        
        /*
         * Provenance type codes
         */
        DB::table('provenance_types')->insert([
            'code' => 'W',
            'label' => '(W) Accession of wild source',
            'description' => 'Accessions which originate from material collected 
                in the wild. The accession has not been propagated further, 
                except in the case of plants that may have been grown on from the 
                original stock. The accession may have come directly from the wild, 
                or from a botanic garden or gene bank acting as a distribution 
                centre. Recent accessions in this category should have 
                accompanying collection data, but the category may also include 
                older accessions which are known to be of direct wild origin 
                but which do not have such additional data.',
        ]);
        
        DB::table('provenance_types')->insert([
            'code' => 'Z',
            'label' => '(Z) Propagule(s) from a wild source plant in cultivation',
            'description' => 'Accessions derived by propagation directly from an original wild source plant. The method of propagation must be recorded in the Propagation History field. If the propagation is not directly from the original wild source plant, a complete history of the intermediate propagation steps must be known, otherwise the accession should placed in the following category.',
        ]);
        
        DB::table('provenance_types')->insert([
            'code' => 'G',
            'label' => '(G) Accession not of wild source',
            'description' => 'Accessions derived from cultivated plants where the immediate source plant does not have a propagation history that can be traced in detail to a wild plant. This category normally includes all cultivars.',
        ]);
        
        DB::table('provenance_types')->insert([
            'code' => 'U',
            'label' => '(U) Unknown/insufficient data',
            'description' => 'Accessions where there is insufficient data or knowledge to know which of the three above categories applies.',
        ]);
        
        /*
         * Identification statuses
         */
        DB::table('identification_statuses')->insert([
            'code' => '1',
            'label' => '(1) Not Verified',
        ]);
        
        DB::table('identification_statuses')->insert([
            'code' => '2',
            'label' => '(2) Verified by botanist, some uncertainty'
        ]);
        
        DB::table('identification_statuses')->insert([
            'code' => '2E',
            'label' => '(2E) ...',
        ]);
        
        DB::table('identification_statuses')->insert([
            'code' => '2L',
            'label' => '(2L) ...',
        ]);
        
        DB::table('identification_statuses')->insert([
            'code' => '2N',
            'label' => '(2N) ...',
        ]);
        
        DB::table('identification_statuses')->insert([
            'code' => '2X',
            'label' => '(2X) ...',
        ]);
        
        DB::table('identification_statuses')->insert([
            'code' => '3',
            'label' => '(3) Verified by botanist'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('bed_types')->delete();
        DB::table('ranks')->delete();
        DB::table('plant_types')->delete();
        DB::table('provenance_types')->delete();
        DB::table('identification_statuses')->delete();
    }
}
