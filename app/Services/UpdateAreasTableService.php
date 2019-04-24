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
 * Description of UpdateAreasTableService
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class UpdateAreasTableService {
    
    protected $nodeNumber = 1;
    
    public function update()
    {
        $root = DB::table('wgs.wgs_region')->whereNull('parent_id')
                ->select('region_id', 'region_code', 'region_name', 'iso_code', 'geom')
                ->first();
        $id = DB::table('areas')->insertGetId([
            'area_name' => $root->region_name,
            'area_full_name' => $root->region_name,
            'locality_id' => 'TDWG:' . $root->region_code,
            'node_number' => $this->nodeNumber,
            'depth' => 0
        ]);
        $this->node($id, $root->region_id, null, 0);
        DB::table('areas')->where('id', $id)->update([
            'highest_descendant_node_number' => $this->nodeNumber
        ]);
    }
    
    protected function node($parentId, $parentRegionId, $parentFullName, $depth)
    {
        $areas = DB::table('wgs.wgs_region')
                ->where('parent_id', $parentRegionId)
                ->select('region_id', 'region_code', 'region_name', 'iso_code', 'geom')
                ->orderBy('region_name')
                ->get();
        if ($areas) {
            foreach ($areas as $area) {
                $this->nodeNumber++;
                $areaFullName = $parentFullName 
                        ? $parentFullName . '; ' . $area->region_name 
                        : $area->region_name;
                $id = DB::table('areas')->insertGetId([
                    'parent_id' => $parentId,
                    'area_name' => $area->region_name,
                    'area_full_name' => $areaFullName,
                    'locality_id' => 'TDWG:' . $area->region_code,
                    'iso_code' => $area->iso_code,
                    'node_number' => $this->nodeNumber,
                    'depth' => $depth + 1,
                    'geom' => $area->geom
                ]);
                $this->node($id, $area->region_id, $areaFullName, $depth + 1);
                DB::table('areas')->where('id', $id)->update([
                    'highest_descendant_node_number' => $this->nodeNumber
                ]);
            }
        }
    }
}
