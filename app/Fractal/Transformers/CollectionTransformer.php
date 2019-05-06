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

use App\Model\Collection as CollectionModel;
use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection;

/**
 * Description of CollectionTransformer
 * 
 * @OA\Schema(
 *   schema="Collection",
 *   type="object"
 * )
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class CollectionTransformer extends TransformerAbstract
{
    /**
     *
     * @var array
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
     *   property="collectionName",
     *   type="string",
     *   description="Name of the collection"
     * )
     * 
     * @param CollectionModel $collection
     * @return array
     */
    public function transform(CollectionModel $collection)
    {
        return [
            'id' => $collection->id,
            'collectionName' => $collection->name,
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
     * @param CollectionModel $collection
     * @return Collection
     */
    protected function includePlants(CollectionModel $collection)
    {
        return new Collection($collection->plants, new PlantTransformer, 'plants');
    }
    
}
