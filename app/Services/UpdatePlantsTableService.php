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
 * Description of UpdatePlantsTableService
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class UpdatePlantsTableService extends UpdateTableService
{
    
    public function update()
    {
        $select = "SELECT 'Melbourne' AS location, concat('RBGM ', substr(p.AccessionNo, 3)) AS accession_number, p.PlantMemberNo AS plant_number, p.BedCode AS bed_code,
              b.BedName AS bed_name, p.Grid AS grid_code, p.DatePlanted AS date_planted, 'bed' AS bed_type
            FROM mysql_plantlist p
            JOIN mysql_location b ON p.BedCode=b.BedCode
            UNION
            SELECT 'Cranbourne' AS location,
            concat('RBGC ', substr(p.AccessionNo, 3)), p.PlantMemberNo AS plant_number,
            IF(p.PositionName IS NOT NULL AND lower(p.PositionName) NOT IN ('zz', 'zzz'), p.AdressographCode, p.SubprecinctCode),
            IF(p.PositionName IS NOT NULL AND lower(p.PositionName) NOT IN ('zz', 'zzz'), p.PositionName, p.SubprecinctName),
            NULL, p.DatePlanted,
            IF(p.PositionName IS NOT NULL AND lower(p.PositionName) NOT IN ('zz', 'zzz'), 'bed', 'subprecinct')
            FROM mysql_plantlist_rbgc p
            JOIN mysql_location_rbgc b ON p.AdressographCode=b.AdressographCode";
        $plants = DB::connection('mysql')->select(DB::raw($select));
        if ($plants) {
            foreach ($plants as $row) {
                $bedId = $this->getBedId($row->location, $row->bed_code, $row->bed_type);
                $gridId = $this->getGridId($row->location, $row->grid_code);
                $accessionId = $this->getAccessionId($row->accession_number);
                $r = DB::table('plants as p')
                        ->join('accessions as a', 'p.accession_id', '=', 'a.id')
                        ->leftJoin('beds as b', 'p.bed_id', '=', 'b.id')
                        ->leftJoin('grids as g', 'p.grid_id', '=', 'g.id')
                        ->where('a.accession_number', $row->accession_number)
                        ->where('p.plant_number', $row->plant_number)
                        ->select('b.location', 'p.id', 'a.id as accession_id', 
                                'a.accession_number', 'p.plant_number', 'b.bed_code', 
                                'b.bed_name', 'g.code AS grid_code', 'p.date_planted')
                        ->first();
                if ($r) {
                    if (($row->bed_name != $r->bed_name) ||
                            (($row->grid_code || $r->grid_code) && $row->grid_code != $r->grid_code) ||
                            (($row->date_planted || $r->date_planted) && $row->date_planted != $r->date_planted)) {
                        DB::table('plants')->where('id', $r->id)->update([
                            'plant_number' => $row->plant_number,
                            'date_planted' => $row->date_planted,
                            'accession_id' => $r->accession_id,
                            'grid_id' => $gridId,
                            'bed_id' => $bedId,
                        ]);
                    }
                }
                else {
                    DB::table('plants')->insert([
                            'plant_number' => $row->plant_number,
                            'date_planted' => $row->date_planted,
                            'accession_id' => $accessionId,
                            'grid_id' => $gridId ?: null,
                            'bed_id' => $bedId ?: null,
                    ]);
                }
            }
        }
    }
    
    protected function getBedId($location, $code, $type)
    {
        return DB::table('beds')
                ->where('location', $location)
                ->where('bed_code', $code)
                ->where('bed_type_id', $this->getBedTypeId($type))
                ->value('id');
    }
    
    protected function getGridId($location, $code)
    {
        if ($location === 'Melbourne')
        {
            return DB::table('grids')->where('code', $code)->value('id');
        }
        return false;
    }
    
    protected function getAccessionId($accessionNumber)
    {
        return DB::table('accessions')
                ->where('accession_number', $accessionNumber)
                ->value('id');
    }
}
