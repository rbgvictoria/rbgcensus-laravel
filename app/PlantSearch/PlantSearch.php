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

namespace App\PlantSearch;

use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

/**
 * Description of PlantSearch
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class PlantSearch {
    
    /**
     * 
     * @param Request $request
     * @return Builder
     */
    public static function apply($filter, $sort)
    {
        $query = static::applyDecoratorsFromRequest($filter, (new Plant)->newQuery());
        
        $query = static::applySort($query, $sort);

        return $query;
    }

    /**
     * 
     * @param Request $request
     * @param Builder $query
     * @return Builder
     */
    private static function applyDecoratorsFromRequest($filters, Builder $query)
    {
        if ($filters) {
            foreach ($filters as $filterName => $value) {

                $decorator = static::createFilterDecorator($filterName);

                if (static::isValidDecorator($decorator)) {
                    $query = $decorator::apply($query, $value);
                }

            }
        }
        return $query;
    }

    /**
     * 
     * @param string $name
     * @return string
     */
    private static function createFilterDecorator($name)
    {
        return __NAMESPACE__ . '\\Filters\\' . studly_case($name);
    }

    /**
     * 
     * @param string $decorator
     * @return boolean
     */
    private static function isValidDecorator($decorator)
    {
        return class_exists($decorator);
    }
    
    private static function applySort(Builder $query, $sortField)
    {
        $allowedSortFields = [
            'taxon_name',
            'family',
            'bed_name'
        ];
        $sortDir = 'ASC';
        if (substr($sortField, 0, 1) === '-') {
            $sortField = substr($sortField, 1);
            $sortDir = 'DESC';
        }
        
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'taxon_name';
            $sortDir = 'ASC';
        }
        
        if ($sortField === 'taxon_name') {
            if (!static::isJoined($query, 'accessions')) {
                $query->join('accessions', 'plants.accession_id', '=', 'accessions.id')
                        ->join('taxa', 'accessions.taxon_id', '=', 'taxa.id');
            }
            $query->orderBy('taxa.taxon_name', $sortDir);
        }
        elseif ($sortField === 'family') {
            if (!static::isJoined($query, 'accessions')) {
                $query->join('accessions', 'plants.accession_id', '=', 'accessions.id')
                        ->join('taxa', 'accessions.taxon_id', '=', 'taxa.id');
            }
            if (!static::isJoined($query, 'classification')) {
                $query->leftJoin('classification', 'taxa.id', '=', 'classification.taxon_id');
            }
            $query->orderBy('classification.family', $sortDir);
        }
        elseif ($sortField === 'bed_name') {
            if (!static::isJoined($query, 'beds')) {
                $query->join('beds', 'bed_id', '=', 'beds.id');
            }
            $query->orderBy('beds.bed_name', $sortDir);
        }
        return $query;
    }

    private static function isJoined($query, $table)
    {
        
        $joins = $query->getQuery()->joins;
        if($joins == null) {
            return false;
        }
        foreach ($joins as $join) {
            if ($join->table == $table) {
                return true;
            }
        }
        return false;
    }

}
