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
 * Description of UpdatePlantAttributesTableService
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class UpdatePlantAttributesTableService extends UpdateTableService
{
    
    public function update()
    {
        $attributes = DB::table('attributes')
                ->select('id', DB::raw('coalesce(db_column_name, name) as name'))
                ->get();
        foreach ($attributes as $attribute) {
            $plantAttributes = $this->getPlantAttributes($attribute->name);
            foreach ($plantAttributes as $row) {
                $plantId = $this->getPlantId($row->site_prefix, $row->accession_number, $row->plant_number);
                $value = $this->normalizeWhiteSpace($row->{$attribute->name});
                $existing = DB::table('plant_attributes')
                        ->where('plant_id', $plantId)
                        ->where('attribute_id', $attribute->id)
                        ->select('id', 'value')
                        ->first();
                if ($existing) {
                    if ($existing->value !== $value) {
                        DB::table('plant_attributes')
                                ->where('id', $existing->id)->update([
                            'value' => $value,
                        ]);
                    }
                }
                else {
                    DB::table('plant_attributes')->insert([
                        'plant_id' => $plantId,
                        'attribute_id' => $attribute->id,
                        'value' => $value,
                    ]);
                }
            }
        }
    }
    
    protected function getPlantAttributes($attribute)
    {
        $first = DB::connection('mysql')
                ->table('mysql_plantlist')
                ->whereNotNull($attribute)
                ->select(DB::raw("'RBGM' as site_prefix"), 'AccessionNo as accession_number', 'PlantMemberNo as plant_number', $attribute);
        return DB::connection('mysql')
                ->table('mysql_plantlist_rbgc')
                ->whereNotNull($attribute)
                ->select(DB::raw("'RBGC' as site_prefix"), 'AccessionNo as accession_number', 'PlantMemberNo as plant_number', $attribute)
                ->union($first)
                ->get();
    }
}
