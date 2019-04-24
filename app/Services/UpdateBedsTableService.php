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
 * Description of UpdateBedsTableService
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class UpdateBedsTableService extends UpdateTableService {
    
    public function update()
    {
        $select = "SELECT 'Melbourne' AS location, 'location' AS bed_type, 'Melbourne' AS bed_name, NULL AS bed_code, NULL AS parent_name, NULL AS parent_code, 0 AS restricted
            UNION
            SELECT 'Melbourne', 'section', section, NULL, 'Melbourne', NULL, max(restricted)
            FROM mysql_location l
            GROUP BY l.section
            UNION
            SELECT 'Melbourne', 'bed', BedName, BedCode, section, NULL, restricted
            FROM mysql_location
            WHERE BedCode IS NOT NULL
            GROUP BY BedName, BedCode, section, restricted
            UNION
            SELECT 'Cranbourne', 'location', 'Cranbourne', NULL, NULL, NULL, 0
            UNION
            SELECT 'Cranbourne', 'precinct', PrecinctName, PrecinctCode, 'Cranbourne', NULL, max(restricted)
            FROM mysql_location_rbgc
            GROUP BY PrecinctName, PrecinctCode
            UNION
            SELECT 'Cranbourne', 'subprecinct', SubPrecinctName, SubPrecinctCode, PrecinctName, PrecinctCode, max(restricted)
            FROM mysql_location_rbgc
            GROUP BY SubPrecinctName, SubPrecinctCode, PrecinctName, PrecinctCode
            UNION
            SELECT 'Cranbourne', 'bed', PositionName, AdressographCode, SubPrecinctName, SubPrecinctCode, restricted
            FROM mysql_location_rbgc
            WHERE AdressographCode IS NOT NULL";
        $result = DB::connection('mysql')->select(DB::raw($select));
        if ($result) {
            foreach ($result as $row) {
                switch (true) {
                    case $row->bed_type == 'location':
                        $this->updateLocation($row);
                        break;
                    case $row->location == 'Melbourne' && $row->bed_type == 'section':
                        $this->updateSectionM($row);
                        break;
                    case $row->location == 'Melbourne' && $row->bed_type == 'bed':
                        $this->updateBedM($row);
                        break;
                    case $row->location == 'Cranbourne' && $row->bed_type == 'precinct':
                        $this->updatePrecinctC($row);
                        break;
                    case $row->location == 'Cranbourne' && $row->bed_type == 'subprecinct':
                        $this->updateSubprecinctC($row);
                        break;
                    case $row->location == 'Cranbourne' && $row->bed_type == 'bed':
                        $this->updateBedC($row);
                        break;
                    default:
                        break;
                }
            }
            
            $sites = DB::table('beds')
                    ->whereNull('parent_id')
                    ->select('id', 'bed_name')
                    ->get();
            foreach ($sites as $site) {
                DB::table('beds')->where('id', $site->id)->update([
                    'bed_full_name' => $site->bed_name,
                ]);
                $this->updateFullNameString($site->id, $site->bed_name);
            }
        }
    }
    
    protected function updateLocation($data) {
        $bedTypeId = $this->getBedTypeId('location');
        $id = DB::table('beds')
                ->where('bed_type_id', $bedTypeId)
                ->where('bed_name', $data->bed_name)
                ->value('id');
        if (!$id) {
            DB::table('beds')->insert([
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'bed_name' => $data->bed_name,
                'bed_code' => null,
                'bed_type_id' => $bedTypeId,
                'location' => $data->bed_name,
            ]);
        }
    }
    
    protected function updateSectionM($data) {
        $bedTypeId = $this->getBedTypeId('section');
        $parentId = DB::table('beds')
                ->where('bed_type_id', $this->getBedTypeId('location'))
                ->where('bed_name', 'Melbourne')
                ->value('id');
        $row = DB::table('beds')
                ->where('bed_type_id', $bedTypeId)
                ->where('location', 'Melbourne')
                ->where('bed_name', $data->bed_name)
                ->select('id', 'is_restricted')
                ->first();
        if ($row) {
            if ((!$row->is_restricted && $data->restricted) 
                    || ($row->is_restricted && !$data->restricted)) {
                DB::table('beds')->where('id', $row->id)->update([
                    'updated_at' => DB::raw('NOW()'),
                    'is_restricted' => abs($data->restricted)
                ]);
            }
        }
        else {
            DB::table('beds')->insert([
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'bed_name' => $data->bed_name,
                'bed_code' => $data->bed_code,
                'parent_id' => $parentId,
                'bed_type_id' => $bedTypeId,
                'is_restricted' => abs($data->restricted),
                'location' => 'Melbourne',
            ]);
        }
    }
    
    protected function updateBedM($data) {
        $bedTypeId = $this->getBedTypeId('bed');
        $parentId = DB::table('beds')
                ->where('bed_type_id', $this->getBedTypeId('section'))
                ->where('location', 'Melbourne')
                ->value('id');
        $row = DB::table('beds')
                ->where('bed_type_id', $bedTypeId)
                ->where('location', 'Melbourne')
                ->where('bed_code', $data->bed_code)
                ->select('id', 'bed_name', 'is_restricted', 'parent_id')
                ->first();
        if ($row) {
            if (!$row->parent_id || $row->parent_id != $parentId ||
                    $data->bed_name != $row->bed_name ||
                    $data->restricted != $row->is_restricted) {
                DB::table('beds')->where('id', $row->id)->update([
                    'updated_at' => DB::raw('NOW()'),
                    'bed_name' => $data->bed_name,
                    'parent_id' => $parentId,
                    'is_restricted' => abs($data->restricted)
                ]);
            }
        }
        else {
            DB::table('beds')->insert([
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'bed_name' => $data->bed_name,
                'bed_code' => $data->bed_code,
                'bed_type_id' => $bedTypeId,
                'parent_id' => $parentId,
                'is_restricted' => abs($data->restricted),
                'location' => 'Melbourne',
            ]);
        }
    }
    
    protected function updatePrecinctC($data) {
        $bedTypeId = $this->getBedTypeId('precinct');
        $parentId = DB::table('beds')
                ->where('bed_type_id', $this->getBedTypeId('location'))
                ->where('bed_name', 'Cranbourne')
                ->value('id');
        $row = DB::table('beds')
                ->where('bed_type_id', $bedTypeId)
                ->where('location', 'Cranbourne')
                ->where('bed_name', $data->bed_name)
                ->select('id', 'is_restricted')
                ->first();
        if ($row) {
            if ((!$row->is_restricted && $data->restricted) 
                    || ($row->is_restricted && !$data->restricted)) {
                DB::table('beds')->where('id', $row->id)->update([
                    'updated_at' => DB::raw('NOW()'),
                    'is_restricted' => abs($data->restricted),
                ]);
            }
        }
        else {
            DB::table('beds')->insert([
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'bed_name' => $data->bed_name,
                'bed_code' => $data->bed_code,
                'bed_type_id' => $bedTypeId,
                'parent_id' => $parentId,
                'is_restricted' => abs($data->restricted),
                'location' => 'Cranbourne',
            ]);
        }
    }
    
    protected function updateSubprecinctC($data) {
        $bedTypeId = $this->getBedTypeId('subprecinct');
        $parentId = DB::table('beds')
                ->where('bed_type_id', $this->getBedTypeId('precinct'))
                ->where('location', 'Cranbourne')
                ->where('bed_code', $data->parent_code)
                ->value('id');
        $row = DB::table('beds')
                ->where('bed_type_id', $bedTypeId)
                ->where('location', 'Cranbourne')
                ->where('bed_code', $data->bed_code)
                ->select('id', 'bed_name', 'is_restricted', 'parent_id')
                ->first();
        if ($row) {
            if (!$row->parent_id || $row->parent_id != $parentId ||
                    $data->bed_name != $row->bed_name ||
                    $data->restricted != $row->is_restricted) {
                DB::table('beds')->where('id', $row->id)->update([
                    'updated_at' => DB::raw('NOW()'),
                    'bed_name' => $data->bed_name,
                    'parent_id' => $parentId,
                    'is_restricted' => abs($data->restricted),
                ]);
            }
        }
        else {
            DB::table('beds')->insert([
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'bed_name' => $data->bed_name,
                'bed_code' => $data->bed_code,
                'bed_type_id' => $bedTypeId,
                'parent_id' => $parentId,
                'is_restricted' => abs($data->restricted),
                'location' => 'Cranbourne',
            ]);
        }
    }
    
    protected function updateBedC($data) {
        $bedTypeId = $this->getBedTypeId('bed');
        $parentId = DB::table('beds')
                ->where('bed_type_id', $this->getBedTypeId('subprecinct'))
                ->where('location', 'Cranbourne')
                ->where('bed_code', $data->parent_code)
                ->value('id');
        $row = DB::table('beds')
                ->where('bed_type_id', $bedTypeId)
                ->where('location', 'Cranbourne')
                ->where('bed_code', $data->bed_code)
                ->select('id', 'bed_name', 'is_restricted', 'parent_id')
                ->first();
        if ($row) {
            if (!$row->parent_id || $row->parent_id != $parentId ||
                    $data->bed_name != $row->bed_name ||
                    $data->restricted != $row->is_restricted) {
                DB::table('beds')->where('id', $row->id)->update([
                    'updated_at' => DB::raw('NOW()'),
                    'bed_name' => $data->bed_name ?: 'Bed with no name',
                    'parent_id' => $parentId,
                    'is_restricted' => abs($data->restricted),
                ]);
            }
        }
        else {
            DB::table('beds')->insert([
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'bed_name' => $data->bed_name ?: 'Bed with no name',
                'bed_code' => $data->bed_code,
                'bed_type_id' => $bedTypeId,
                'parent_id' => $parentId,
                'is_restricted' => abs($data->restricted),
                'location' => 'Cranbourne',
            ]);
        }
    }
    
    public function nestedSets()
    {
        $this->nodeNumber = 0;
        
        $roots = DB::table('beds')
                ->whereNull('parent_id')
                ->pluck('id');
        
        foreach ($roots as $id) {
            $this->createNode($id, 0);
        }
    }
    
    protected function createNode($parentId, $depth)
    {
        $this->nodeNumber++;
        DB::table('beds')->where('id', $parentId)->update([
            'node_number' => $this->nodeNumber,
            'depth' => $depth,
        ]);
        $children = DB::table('beds')
                ->where('parent_id', $parentId)
                ->orderBy('bed_name')
                ->pluck('id');
        if ($children) {
            foreach ($children as $id) {
                $this->createNode($id, $depth + 1);
            }
        }
        DB::table('beds')->where('id', $parentId)->update([
            'highest_descendant_node_number' => $this->nodeNumber,
        ]);
    }
    
    public function updateFullNameString($parentId, $parentFullName)
    {
        $beds = DB::table('beds')
                ->where('parent_id', $parentId)
                ->select('id', 'bed_name')
                ->get();
        if ($beds) {
            foreach ($beds as $bed) {
                $fullName = $parentFullName . '; ' . $bed->bed_name;
                DB::table('beds')->where('id', $bed->id)->update([
                    'bed_full_name' => $fullName,
                ]);
                $this->updateFullNameString($bed->id, $fullName);
            }
        }
    }

}
