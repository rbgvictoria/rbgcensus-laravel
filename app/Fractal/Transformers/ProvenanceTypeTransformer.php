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

use App\Models\ProvenanceType;
use League\Fractal\TransformerAbstract;

/**
 * Description of ProvenanceTypeController
 * 
 * @OA\Schema(
 *   schema="ProvenanceType",
 *   type="object"
 * )
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class ProvenanceTypeTransformer extends TransformerAbstract
{
    /**
     * @OA\property(
     *   property="id",
     *   type="integer",
     *   format="int64"
     * ),
     * @OA\Property(
     *   property="label",
     *   type="string"
     * ),
     * @OA\Property(
     *   property="description",
     *   type="string"
     * )
     * 
     * @param ProvenanceType $provenanceType
     * @return array
     */
    public function transform(ProvenanceType $provenanceType)
    {
        return [
            'id' => $provenanceType->code,
            'label' => $provenanceType->label,
            'description' => $provenanceType->description
        ];
    }
}
