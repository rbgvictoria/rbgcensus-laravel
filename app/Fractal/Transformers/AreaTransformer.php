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

namespace App\Fractal\Transformers;

use App\Models\Area;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

/**
 * Description of AreaTransformer
 *
 * @OA\Schema (
 *   schema="Area",
 *   type="object"
 * )
 * 
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class AreaTransformer extends TransformerAbstract
{
    /**
     *
     * @var \Array
     */
    protected $availableIncludes = [
        'parent',
    ];
    
    /**
     * @OA\Property(
     *   property="id",
     *   type="integer",
     *   format="int64"
     * ),
     * @OA\Property(
     *   property="localityId",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="areaName",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="areaFullName",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="isoCode",
     *   type="string"
     * ),
     * 
     * @param \App\Models\Area $area
     * @return \Array
     */
    public function transform(Area $area)
    {
        return [
            'id' => $area->id,
            'localityId' => $area->locality_id,
            'areaName' => $area->area_name,
            'areaFullName' => $area->area_full_name,
            'isoCode' => $area->iso_code,
        ];
    }
    
    /**
     * @OA\Property(
     *   property="parent",
     *   ref="#/components/schemas/Area"
     * )
     * 
     * @param \App\Models\Area $area
     * @return \League\FRactal\Resource\Item
     */
    protected function includeParent(Area $area)
    {
        if ($area->parent) {
            return new Item($area->parent, new AreaTransformer, 'parentArea');
        }
    }
}
