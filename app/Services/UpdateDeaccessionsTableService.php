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
 * Description of UpdateDeaccessionsTableService
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class UpdateDeaccessionsTableService {
    
    public function update()
    {
        $select = "SELECT p.id, a.accession_number, p.plant_number
            FROM plants p
            JOIN accessions a ON p.accession_id=a.id
            LEFT JOIN deaccessions d ON p.id=d.plant_id
            WHERE d.id IS NULL";
        $plants = DB::select($select);
        foreach ($plants as $plant) {
            list($site, $accessionNumber) = explode(' ', $plant->accession_number);
            $table = $site === 'RBGM' ? 'mysql_plantlist' : 'mysql_plantlist_rbgc';
            $count = DB::connection('mysql')
                    ->table($table)
                    ->where(DB::raw('SUBSTRING(AccessionNo, 3)'), $accessionNumber)
                    ->where('PlantMemberNo', $plant->plant_number)
                    ->count();
            if (!$count) {
                DB::table('deaccessions')->insert(['plant_id' => $plant->id]);
            }
        }
    }
}
