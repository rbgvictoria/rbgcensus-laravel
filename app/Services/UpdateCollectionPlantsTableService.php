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

use Illuminate\Support\Facades\DB;

/**
 * Description of UpdateCollectionPlantsTableService
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class UpdateCollectionPlantsTableService
{
    
    public function update()
    {
        $collections = $this->getCollections();
        foreach ($collections as $collection) {
            $query = DB::connection('mysql')->table('mysql_plantlist')
                    ->select(DB::raw("CONCAT('RBGM ', SUBSTRING(AccessionNo, 3)) "
                            . "AS accession_number"), 
                            'PlantMemberNo as plant_number');
            if ($collection->name = 'Commemorative trees') {
                $query->whereNotNull('NationalTrustStatus');
            }
            else {
                $query->whereRaw('Commemorative IS NOT NULL')
                        ->orWhereRaw('InMemoryOf IS NOT NULL');
            }
            $plants = $query->get();
            foreach ($plants as $plant) {
                $plantId = $this->getPlantId($plant->accession_number, $plant->plant_number);
                $collectionPlant = $this->findCollectionPlant($collection->id, $plantId);
                if (!$collectionPlant && $plantId) {
                    DB::table('collection_plants')->insert([
                        'collection_id' => $collection->id,
                        'plant_id' => $plantId,
                    ]);
                }
            }
        }
    }
    
    protected function getCollections()
    {
        $cols = [
            'Commemorative trees',
            'National Trust classified trees',
        ];

        return DB::table('collections')
                ->whereIn('name', $cols)
                ->select('id', 'name')
                ->get();
    }
    
    protected function getPlantId($accessionNumber, $plantNumber)
    {
        return DB::table('plants as p')
                ->join('accessions as a', 'p.accession_id', '=', 'a.id')
                ->where('a.accession_number', $accessionNumber)
                ->where('p.plant_number', $plantNumber)
                ->value('p.id');
    }

    protected function findCollectionPlant($collectionId, $plantId)
    {
        return DB::table('collection_plants')
                ->where('collection_id', $collectionId)
                ->where('plant_id', $plantId)
                ->first();
    }
}
