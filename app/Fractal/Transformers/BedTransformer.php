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

use App\Models\Bed;
use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

/**
 * Description of BedTransformer
 * 
 * @OA\Schema(
 *   schema="Bed",
 *   type="object"
 * )
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class BedTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'bedType',
        'site',
        'precinct',
        'subprecinct',
        'plants'
    ];
    
    protected $defaultIncludes = [];
    
    /**
     * @OA\Property(
     *   property="id",
     *   type="integer",
     *   format="int64"
     * ),
     * @OA\Property(
     *   property="bedName",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="bedCode",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="bedFullName",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="isRestricted",
     *   type="boolean"
     * ),
     * 
     * 
     * @param Bed $bed
     * @return array
     */
    public function transform(Bed $bed)
    {
        return [
            'id' => $bed->id,
            'bedName' => $bed->bed_name,
            'bedCode' => $bed->bed_code,
            'bedFullName' => $bed->bed_full_name,
            'isRestricted' => $bed->is_restricted,
        ];
    }
    
    /**
     * @OA\Property(
     *   property="bedType",
     *   ref="#/components/schemas/BedType"
     * )
     * 
     * @param \App\Models\Bed $bed
     * @return \League\Fractal\Resource\Item
     */
    protected function includeBedType(Bed $bed)
    {
        return new Item($bed->bedType, new BedTypeTransformer, 'bedType');
    }
    
    /**
     * @OA\Property(
     *   property="site",
     *   ref="#/components/schemas/Bed"
     * )
     * 
     * @param \App\Models\Bed $bed
     * @return \League\Fractal\Resource\Item
     */
    protected function includeSite(Bed $bed)
    {
        $site = Bed::join('bed_types', 'beds.bed_type_id', '=', 'bed_types.id')
                ->where('beds.node_number', '<', $bed->node_number)
                ->where('beds.highest_descendant_node_number', '>=', 
                        $bed->node_number)
                ->where('bed_types.name', 'location')
                ->first();
        if ($site) {
            return new Item($site, new BedTransformer, 'site');
        }
    }
    
    /**
     * @OA\Property(
     *   property="precinct",
     *   ref="#/components/schemas/Bed"
     * )
     * 
     * @param \App\Models\Bed $bed
     * @return \League\Fractal\Resource\Item
     */
    protected function includePrecinct(Bed $bed)
    {
        $precinct = Bed::join('bed_types', 'beds.bed_type_id', '=', 'bed_types.id')
                ->where('beds.node_number', '<', $bed->node_number)
                ->where('beds.highest_descendant_node_number', '>=', 
                        $bed->node_number)
                ->where('bed_types.name', 'precinct')
                ->first();
        if ($precinct) {
            return new Item($site, new BedTransformer, 'precinct');
        }
    }
    
    /**
     * @OA\Property(
     *   property="subprecinct",
     *   ref="#/components/schemas/Bed"
     * )
     * 
     * @param \App\Models\Bed $bed
     * @return \League\Fractal\Resource\Item
     */
    protected function includeSubprecinct(Bed $bed)
    {
        $subprecinct = Bed::join('bed_types', 'beds.bed_type_id', '=', 'bed_types.id')
                ->where('beds.node_number', '<', $bed->node_number)
                ->where('beds.highest_descendant_node_number', '>=', 
                        $bed->node_number)
                ->where('bed_types.name', 'subprecinct')
                ->first();
        if ($subprecinct) {
            return new Item($site, new BedTransformer, 'subprecinct');
        }
    }
    
    /**
     * @OA\Property(
     *   property="plants",
     *   type="array",
     *   @OA\Items(
     *     ref="#/components/schemas/Plant"
     *   )
     * )
     * 
     * @param \App\Models\Bed $bed
     * @return \League\Fractal\Resource\Collection
     */
    protected function includePlants(Bed $bed)
    {
        return new Collection($bed->plants, new PlantTransformer, 'plants');
    }
}
