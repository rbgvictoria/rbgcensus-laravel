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

namespace App\Fractal;

use App\Fractal\Serializers\DataArraySerializer;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use League\Fractal\Manager;
use League\Fractal\TransformerAbstract;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

/**
 * Description of Fractal
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class FractalManager {

    protected $manager;
    
    public function __construct()
    {
        $this->manager = new Manager();
        $this->manager->setSerializer(new DataArraySerializer);
        $this->parseIncludes();
        $this->parseExcludes();
        $this->parseFieldsets();
    }
    
    protected function parseIncludes()
    {
        if (\request()->input('include')) {
            $this->manager->parseIncludes(\request()->input('include'));
        }
    }
    
    protected function parseExcludes()
    {
        if (\request()->input('exclude')) {
            $this->manager->parseExcludes(\request()->input('exclude'));
        }
    }
    
    protected function parseFieldsets()
    {
        if (\request()->input('fields')) {
            $fields = explode(';', \request()->input('fields'));
            $fieldsets = [];
            foreach ($fields as $field) {
                if (strpos($field, '[') !== false) {
                    list($resource, $values) = explode('[', trim($field, '] '));
                    foreach (explode(',', $values) as $value) {
                        $fieldsets[$resource][] = $value;
                    }
                }
                else {
                    list($resource, $value) = explode('.', $field);
                    $fieldsets[$resource][] = $value;
                }
            }
            foreach ($fieldsets as $index => $arr) {
                $fieldsets[$index] = implode(',', $arr);
            }
            $this->manager->parseFieldsets($fieldsets);
        }
    }
    
    /**
     * 
     * @param type $item
     * @param TransformerAbstract $transformer
     * @param type $resourceKey
     * @return \Illuminate\Http\Response
     */
    public function item($item, TransformerAbstract $transformer, $resourceKey)
    {
        $resource = new Item($item, $transformer, $resourceKey);
        $data = $this->manager->createData($resource)->toArray();
        return response()->json($data);
    }
    
    public function collection($collection, 
            TransformerAbstract $transformer, $resourceKey)
    {
        $resource = new Collection($collection, $transformer, $resourceKey);
        $data = $this->manager->createData($resource)->toArray();
        return response()->json($data);
    }
    
    public function page(Request $request, LengthAwarePaginator $paginator, 
            TransformerAbstract $transformer, $resourceKey=null)
    {
        $queryParams = array_diff_key($request->all(), array_flip(['page']));
        $paginator->appends($queryParams);
        $paginatorAdapter = new IlluminatePaginatorAdapter($paginator);
        $collection = $paginator->getCollection();
        $resource = new Collection($collection, $transformer, $resourceKey);
        $resource->setPaginator($paginatorAdapter);
        $data = $this->manager->createData($resource)->toArray();
        return response()->json($data);
    }
    
}
