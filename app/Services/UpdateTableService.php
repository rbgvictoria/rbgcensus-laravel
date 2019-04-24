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
 * Description of UpdateTableService
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class UpdateTableService {
    
    protected function getRankId($rank)
    {
        return DB::table('ranks')->where('name', $rank)->value('id');
    }
    
    protected function getPlantTypeId($plantType)
    {
        return DB::table('plant_types')->where('abbreviation', $plantType)
                ->value('id');
    }
    
    protected function getBedTypeId($bedType)
    {
        return DB::table('bed_types')->where('name', $bedType)->value('id');
    }
    
    protected function normalizeWhiteSpace($str)
    {
        return preg_replace('/\s+/', ' ', $str);
    }

    protected function getPlantId($sitePrefix, $accessionNumber, $plantNumber)
    {
        return DB::table('plants as p')
                ->join('accessions as a', 'p.accession_id', '=', 'a.id')
                ->where('a.accession_number', $sitePrefix . ' ' . substr($accessionNumber, 2))
                ->where('p.plant_number', $plantNumber)
                ->value('p.id');
    }
    
}
