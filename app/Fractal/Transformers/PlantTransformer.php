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

use App\Models\Plant;
use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

/**
 * Description of PlantTransformer
 * 
 * @OA\Schema(
 *   schema="Plant",
 *   type="object"
 * )
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class PlantTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'accession',
        'bed',
        'grid',
        'plantAttributes'
    ];
    
    /**
     *
     * @var \Array 
     */
    protected $defaultIncludes = [];
    
    /**
     * @OA\Property(
     *   property="id",
     *   type="int64"
     * ),
     * @OA\Property(
     *   property="plantNumber",
     *   type="string"
     * ),
     * 
     * @param \App\Models\Plant $plant
     * @return \Array
     */
    public function transform(Plant $plant) 
    {
        return [
            'id' => $plant->id,
            'plantNumber' => $plant->accessionNumber . '.' . $plant->plant_number,
        ];
    }
    
    /**
     * @OA\Property(
     *   property="accession",
     *   ref="#/components/schemas/Accession"
     * )
     * 
     * @param \App\Models\Plant $plant
     * @return \League\Fractal\Resource\Item
     */
    protected function includeAccession(Plant $plant)
    {
        return new Item($plant->accession, new AccessionTransformer, 'accession');
    }
    
    /**
     * @OA\Property(
     *   property="bed",
     *   ref="#/components/schemas/Bed"
     * )
     * 
     * @param \App\Models\Plant $plant
     * @return \League\Fractal\Resource\Item
     */
    protected function includeBed(Plant $plant)
    {
        return new Item($plant->bed, new BedTransformer, 'bed');
    }
    
    /**
     * @OA\Property(
     *   property="grid",
     *   ref="#/components/schemas/Grid"
     * )
     * 
     * @param Plant $plant
     * @return Item|\League\Fractal\Resource\NullResource
     */
    protected function includeGrid(Plant $plant)
    {
        if ($plant->grid) {
            return new Item($plant->grid, new GridTransformer, 'grid');
        }
        return new \League\Fractal\Resource\NullResource();
    }
    
    /**
     * @OA\Property(
     *   property="plantAttributes",
     *   type="array",
     *   @OA\Items(
     *     ref="#/components/schemas/PlantAttribute"
     *   )
     * )
     * 
     * @param Plant $plant
     * @return Collection
     */
    protected function includePlantAttributes(Plant $plant)
    {
        return new Collection($plant->plantAttributes, 
                new PlantAttributeTransformer, 'plantAttributes');
    }
}
