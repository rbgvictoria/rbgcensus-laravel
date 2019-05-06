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

use App\Models\Taxon;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

/**
 * Description of TaxonTransformer
 * 
 * @OA\Schema(
 *   schema="Taxon",
 *   type="object"
 * )
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class TaxonTransformer extends TransformerAbstract 
{
    protected $availableIncludes = [
        'parent',
        'higherClassification',
        'family',
        'children',
        'naturalDistribution',
        'accessions',
    ];
    
    protected $defaultIncludes = [
        'rank',
    ];
    
    /**
     * @OA\property(
     *   property="id",
     *   type="integer",
     *   format="int64"
     * ),
     * @OA\Property(
     *   property="taxonName",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="authorship",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="vernacularName",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="isNativeToAustralia",
     *   type="boolean"
     * ),
     * @OA\Property(
     *   property="isEndangered",
     *   type="boolean"
     * ),
     * @OA\Property(
     *   property="hideFromPublic",
     *   type="boolean"
     * ),
     * 
     * @param \App\Models\Taxon $taxon
     * @return Array
     */
    public function transform(Taxon $taxon) 
    {
        return [
            'id' => $taxon->id,
            'taxonName' => $taxon->taxon_name,
            'title' => ($taxon->author) ? $taxon->taxon_name . ' ' . 
                    $taxon->author : $taxon->taxon_name,
            'authorship' => $taxon->author,
            'vernacularName' => $taxon->vernacular_name,
            'isNativeToAustralia' => $taxon->isAustralianNative,
            'isEndangered' => $taxon->isEndangered,
            'hideFromPublic' => $taxon->hideFromPublicDisplay
        ];
    }
    
    /**
     * @OA\Property(
     *   property="parent",
     *   ref="#/components/schemas/Taxon"
     * )
     * 
     * @param \App\Models\Taxon $taxon
     * @return \League\Fractal\Resource\Item
     */
    protected function includeParent(Taxon $taxon)
    {
        $parent = $taxon->parent;
        if ($parent) {
            return new Item($parent, 
                    new TaxonTransformer, 'parent');
        }
    }
    
    /**
     * @OA\Property(
     *   property="children",
     *   type="array",
     *   @OA\Items(
     *     ref="#/components/schemas/Taxon"
     *   )
     * )
     * 
     * @param \App\Models\Taxon $taxon
     * @return \League\Fractal\Resource\Collection
     */
    protected function includeChildren(Taxon $taxon)
    {
        return new Collection($taxon->children, 
                new TaxonTransformer, 'children');
    }
    
    /**
     * @OA\Property(
     *   property="higherClassification",
     *   type="array",
     *   @OA\Items(
     *     ref="#/components/schemas/Taxon"
     *   )
     * )
     * 
     * @param \App\Models\Taxon $taxon
     * @return \League\Fractal\Resource\Collection
     */
    protected function includeHigherClassification(Taxon $taxon)
    {
        return new Collection($taxon->higherClassification, new TaxonTransformer, 
                'higherClassification');
    }
    
    /**
     * @OA\Property(
     *   property="family",
     *   ref="#/components/schemas/Taxon"
     * )
     * 
     * @param Taxon $taxon
     * @return Item
     */
    protected function includeFamily(Taxon $taxon)
    {
        if ($taxon->family) {
            return new Item($taxon->family, new TaxonTransformer, 'family');
        }
    }
    
    /**
     * @OA\Property(
     *   property="rank",
     *   ref="#/components/schemas/Rank"
     * )
     * 
     * @param \App\Models\Taxon $taxon
     * @return \League\Fractal\Resource\Item
     */
    protected function includeRank(Taxon $taxon)
    {
        if ($taxon->rank) {
            return new Item($taxon->rank, new RankTransformer, 'rank');
        }
    }
    
    /**
     * @OA\Property(
     *   property="naturalDistribution",
     *   type="array",
     *   @OA\Items(
     *     ref="#/components/schemas/Area"
     *   )
     * )
     * 
     * @param \App\Models\Taxon $taxon
     * @return \League\Fractal\Resource\Collection
     */
    protected function includeNaturalDistribution(Taxon $taxon)
    {
        return new Collection($taxon->areas, new AreaTransformer, 
                'naturalDistribution');
    }
    
    /**
     * @OA\Property(
     *   property="accessions",
     *   type="array",
     *   @OA\Items(
     *     ref="#/components/schemas/Accession"
     *   )
     * )
     * 
     * @param \App\Models\Taxon $taxon
     * @return \League\Fractal\Resource\Collection
     */
    protected function includeAccessions(Taxon $taxon)
    {
        return new Collection($taxon->accessions, new AccessionTransformer, 
                'accessions');
    }
}
