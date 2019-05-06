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

use App\Models\Accession;
use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

/**
 * Description of AccessionTransformer
 * 
 * @OA\Schema (
 *   schema="Accession",
 *   type="object"
 * )
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class AccessionTransformer extends TransformerAbstract
{
    /**
     * 
     * @var \Array
     */
    protected $availableIncludes = [
        'taxon',
        'plants'.
        'provenanceType',
        'identificationStatus',
    ];
    
    protected $defaultIncludes = [];
    
    /**
     * @OA\Property(
     *   property="id",
     *   type="integer",
     *   format="int64"
     * ),
     * @OA\Property(
     *   property="accessionNumber",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="collectorName",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="provenanceHistory",
     *   type="string"
     * )
     * 
     * @param \App\Models\Accession $accession
     * @return \Array
     */
    public function transform(Accession $accession)
    {
        return [
            'id' => $accession->id,
            'accessionNumber' => $accession->accession_number,
            'collectorName' => $accession->collector_name,
            'provenanceHistory' => $accession->provenance_history,
        ];
    }
    
    /**
     * @OA\Property(
     *   property="taxon",
     *   ref="#/components/schemas/Taxon"
     * )
     * 
     * @param \App\Models\Accession $accession
     * @return \League\Fractal\Resource\Item
     */
    public function includeTaxon(Accession $accession)
    {
        return new Item($accession->taxon, new TaxonTransformer, 'taxon');
    }
    
    /**
     * @OA\Property(
     *   property="provenanceType",
     *   ref="#/components/schemas/ProvenanceType"
     * )
     * 
     * @param \App\Models\Accession $accession
     * @return \League\Fractal\Resource\Item
     */
    public function includeProvenanceType(Accession $accession)
    {
        if ($accession->provenanceType) {
            return new Item($accession->provenanceType, 
                    new ProvenanceTypeTransformer(), 'provenanceType');
        }
    }
    
    /**
     * @OA\Property(
     *   property="identificationStatus",
     *   ref="#/components/schemas/IdentificationStatus"
     * )
     * 
     * @param \App\Models\Accession $accession
     * @return \League\Fractal\Resource\Item
     */
    public function includeIdentificationStatus(Accession $accession)
    {
        if ($accession->identificationStatus) {
            return new Item($accession->identificationStatus, 
                    new IdentificationStatusTransformer, 'identificationStatus');
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
     * @param \App\Models\Accession $accession
     * @return \Leaugue\Fractal\Resource\Collection
     */
    protected function includePlants(Accession $accession)
    {
        return new Collection($accession->plants, new PlantTransformer, 
                'plants');
    }
}
