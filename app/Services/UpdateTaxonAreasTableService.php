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
 * Description of UpdateTaxonAreasTableService
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class UpdateTaxonAreasTableService 
{
    
    public function update()
    {
        $taxonAreas = $this->getTaxonAreas();
        foreach ($taxonAreas as $rec) {
            $taxonId = $this->findTaxon($rec->species_id);
            $areaId = $this->findArea('TDWG:' . $rec->area_code);
            if ($taxonId && $areaId) {
                $taxonArea = $this->findTaxonArea($taxonId, $areaId);
                if (!$taxonArea) {
                    $this->insertTaxonArea($taxonId, $areaId);
                }
            }
        } 
        
    }
    
    protected function getTaxonAreas()
    {
        return DB::connection('mysql')->table('mysql_tblSpeciesTDWG')
                ->select('SpeciesID as species_id', 
                        DB::raw('coalesce(level_4_code,level_3_code) as area_code'))
                ->get();
    }
    
    protected function findTaxon($speciesId)
    {
        return DB::table('taxa')->where('lcd_species_id', $speciesId)
                ->value('id');
    }
    
    protected function findArea($localityId)
    {
        return DB::table('areas')->where('locality_id', $localityId)
                ->value('id');
    }
    
    protected function findTaxonArea($taxonId, $areaId)
    {
        return DB::table('taxon_areas')->where('taxon_id', $taxonId)
                ->where('area_id', $areaId)
                ->first();
    }
    
    protected function insertTaxonArea($taxonId, $areaId)
    {
        DB::table('taxon_areas')->insert([
            'taxon_id' => $taxonId,
            'area_id' => $areaId,
        ]);
    }
}
