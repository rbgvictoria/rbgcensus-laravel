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

use App\Models\Grid;
use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection;

/**
 * Description of GridTransformer
 * 
 * @OA\Schema(
 *   schema="Grid",
 *   type="object"
 * )
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class GridTransformer extends TransformerAbstract
{
    /**
     * 
     * @var \Array 
     */
    protected $availableIncludes = [
        'plants',
    ];
    
    /**
     * @OA\Property(
     *   property="id",
     *   type="integer",
     *   format="int64"
     * ),
     * @OA\Property(
     *   property="gridCode",
     *   type="string"
     * )
     * 
     * @param \App\Models\Grid $grid
     * @return \Array
     */
    public function transform(Grid $grid)
    {
        return [
            'id' => $grid->id,
            'gridCode' => $grid->code,
        ];
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
     * @param \App\Models\Grid $grid
     * @return \League\Fractal\Resource\Collection
     */
    public function includePlants(Grid $grid)
    {
        return new Collection($grid->plants, new PlantTransformer, 'plants');
    }
}
