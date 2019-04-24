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
 * Description of UpdateGridsTableService
 *
 * @author Niels Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class UpdateGridsTableService 
{
    
    public function update()
    {
        $select = "SELECT DISTINCT Grid FROM mysql_plantlist ORDER BY Grid";
        $result = DB::connection('mysql')->select(DB::raw($select));
        if ($result) {
            foreach ($result as $row) {
                $grid = str_replace(' ', '', $row->Grid);
                $id = DB::table('grids')->where('code', $grid)->value('id');
                if (!$id) {
                    $geom = DB::table('aux.grid_polygon')
                            ->where('gridname', $grid)
                            ->select(DB::raw('ST_Transform(geom, 900913) as geom, ST_Transform(geom, 28355) as geom_mga'))
                            ->first();
                    DB::table('grids')->insert([
                        'created_at' => DB::raw('NOW()'),
                        'updated_at' => DB::raw('NOW()'),
                        'code' => $grid,
                        'geom' => is_object($geom) ? $geom->geom : null,
                        'geom_mga' => is_object($geom) ? $geom->geom_mga : null,
                    ]);
                }
            }
        }
    }
}
