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

namespace App\PlantSearch\Filters;

use Illuminate\Database\Eloquent\Builder;

class Precinct implements Filter
{

    /**
     * Apply a given search value to the builder instance.
     *
     * @param Builder $builder
     * @param mixed $value
     * @return Builder $builder
     */
    public static function apply(Builder $builder, $value)
    {
        return $builder->whereHas('bed', function ($query) use ($value) {
            $node = \App\Models\Bed::join('bed_types', 'bed_type_id', '=', 'bed_types.id')
                    ->where('bed_name', $value)
                    ->where('bed_types.name', 'precinct')
                    ->select('node_number', 'highest_descendant_node_number')
                    ->first();
            $nodeNumber = $node ? $node->node_number : 0;
            $highestDescendantNodeNumber = $node ? $node->highest_descendant_node_number : 0;
            $query->where('node_number', '>=', $nodeNumber)
                    ->where('node_number', '<=', $highestDescendantNodeNumber);
        });
    }
}