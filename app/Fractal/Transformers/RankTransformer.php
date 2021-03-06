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

use App\Models\Rank;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

/**
 * Description of TaxonRankTransformer
 * 
 * @OA\Schema(
 *   schema="Rank",
 *   type="object"
 * )
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class RankTransformer extends TransformerAbstract {
    
    /**
     *
     * @var \Array
     */
    protected $availableIncludes = [
        'parent',
    ];
    
    /**
     * @OA\property(
     *   property="id",
     *   type="integer",
     *   format="int64"
     * ),
     * @OA\Property(
     *   property="label",
     *   type="string"
     * )
     * 
     * @param \App\Models\Rank $rank
     * @return \Array
     */
    public function transform(Rank $rank) 
    {
        return [
            'id' => $rank->name,
            'label' => ucfirst($rank->name),
        ];
    }
    
    /**
     * @OA\Property(
     *   property="parent",
     *   ref="#/components/schemas/Rank"
     * )
     * 
     * @param Rank $rank
     * @return Item
     */
    public function includeParent(Rank $rank)
    {
        if ($rank->parent) {
            return new Item($rank->parent, new RankTransformer, 
                    'parentRank');
        }
    }
}
