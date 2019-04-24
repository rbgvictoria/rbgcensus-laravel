<?php

/*
 * Copyright 2019 Royal Botanic Gardens Victoria.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

/**
 * Description of LoadHigherClassificationService
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class LoadHigherClassificationService extends UpdateTableService
{
    
    public function createHigherClassificationTable() 
    {
        Schema::create('higher_classification', function (Blueprint $table) {
            $table->string('kingdom', 64);
            $table->string('phylum', 64);
            $table->string('class', 64);
            $table->string('order', 64);
            $table->string('family', 64);
        });
    }
    
    public function loadDataFromCsv($fileName)
    {
        $doc = Storage::get($fileName);
                $stmt = "INSERT INTO higher_classification (kingdom, phylum, class, \"order\", family) 
            VALUES (?, ?, ?, ?, ?)";
        
        foreach (explode("\r\n", $doc) as $index => $line) {
            if ($index > 0) {
                DB::statement($stmt, str_getcsv($line));
            }
        }
    }
    
    public function dropHigherClassificationTable()
    {
        DB::unprepared('DROP TABLE higher_classification');
    }
    
    public function kingdom()
    {
        $kingdoms = DB::table('higher_classification')->distinct()->pluck('kingdom');
        foreach ($kingdoms as $kingdom) {
            $id = DB::table('taxa')->insertGetId([
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'taxon_name' => $kingdom,
                'rank_id' => $this->getRankId('kingdom'),
            ]);
            
            $phyla = DB::table('higher_classification')
                    ->where('kingdom', $kingdom)
                    ->distinct()
                    ->pluck('phylum');
            
            foreach ($phyla as $phylum) {
                $this->phylum($phylum, $id);
            }
        }
    }
    
    protected function phylum($phylum, $parentId)
    {
        $id = DB::table('taxa')->insertGetId([
            'created_at' => DB::raw('NOW()'),
            'updated_at' => DB::raw('NOW()'),
            'parent_id' => $parentId,
            'taxon_name' => $phylum,
            'rank_id' => $this->getRankId('phylum'),
        ]);
        $classes = DB::table('higher_classification')
                ->where('phylum', $phylum)
                ->distinct()
                ->pluck('class');
        foreach($classes as $class) {
            $this->classs($class, $id);
        }
    }
    
    protected function classs($class, $parentId)
    {
        $id = DB::table('taxa')->insertGetId([
            'created_at' => DB::raw('NOW()'),
            'updated_at' => DB::raw('NOW()'),
            'parent_id' => $parentId,
            'taxon_name' => $class,
            'rank_id' => $this->getRankId('class'),
        ]);
        $orders = DB::table('higher_classification')
                ->where('class', $class)
                ->distinct()
                ->pluck('order');
        foreach($orders as $order) {
            $this->order($order, $id);
        }
    }
    
    protected function order($order, $parentId)
    {
        $id = DB::table('taxa')->insertGetId([
            'created_at' => DB::raw('NOW()'),
            'updated_at' => DB::raw('NOW()'),
            'parent_id' => $parentId,
            'taxon_name' => $order,
            'rank_id' => $this->getRankId('order'),
        ]);
        $families = DB::table('higher_classification')
                ->where('order', $order)
                ->distinct()
                ->pluck('family');
        foreach($families as $family) {
            $this->family($family, $id);
        }
    }
    
    protected function family($family, $parentId)
    {
        DB::table('taxa')->insert([
            'created_at' => DB::raw('NOW()'),
            'updated_at' => DB::raw('NOW()'),
            'parent_id' => $parentId,
            'taxon_name' => $family,
            'rank_id' => $this->getRankId('family'),
        ]);        
    }
}
