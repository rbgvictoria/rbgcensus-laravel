<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Fractal\Transformers\CollectionTransformer;
use Illuminate\Http\Request;

class CollectionController extends ApiController
{
    /**
     * @OA\Get(
     *   path="/collections",
     *   summary="List collections",
     *   @OA\Parameter(
     *     ref="#/components/parameters/include"
     *   ),
     *   @OA\Response(
     *     response="200",
     *     description="Successful response.",
     *     @OA\MediaType(
     *       @OA\Schema(
     *         type="array",
     *         @OA\Items(
     *           ref="#/components/schemas/Collection"
     *         )
     *       ),
     *       mediaType="application/json"
     *     )
     *   ),
     * )
     * 
     * @param Request $request
     * @return \App\Fractal\FractalManager
     */
    public function index()
    {
        $collections = Collection::all();
        return $this->fractal->collection($collections, 
                new CollectionTransformer, 'collections');
    }
    
    /**
     * @OA\Get(
     *   path="/collections/{collection}",
     *   summary="Get single collection",
     *   @OA\Parameter(
     *     in="path",
     *     name="collecttion",
     *     description="ID of collection",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Parameter(
     *     ref="#/components/parameters/include"
     *   ),
     *   @OA\Response(
     *     response="200",
     *     description="Successful response.",
     *     @OA\MediaType(
     *       @OA\Schema(
     *         ref="#/components/schemas/Collection"
     *       ),
     *       mediaType="application/json"
     *     )
     *   )
     * )
     * 
     * @param Collection $collection
     * @return \Illuminate\Http\Response
     */
    public function show(Collection $collection)
    {
        return $this->fractal->item($collection, new CollectionTransformer, 
                'collection');
    }
    
    /**
     * @OA\Get(
     *   path="/collections/{collection}/plants",
     *   summary="Get plants in collection",
     *   @OA\Parameter(
     *     in="path",
     *     name="collection",
     *     @OA\Schema(
     *       type="integer"
     *     ),
     *     description="ID of the collection"
     *   ),
     *   @OA\Parameter(
     *     ref="#/components/parameters/per_page"
     *   ),
     *   @OA\Parameter(
     *     ref="#/components/parameters/page"
     *   ),
     *   @OA\Parameter(
     *     ref="#/components/parameters/include"
     *   ),
     *   @OA\Response(
     *     response="200",
     *     description="Successful response.",
     *     @OA\MediaType(
     *       @OA\Schema(
     *         type="array",
     *         @OA\Items(
     *           ref="#/components/schemas/Collection"
     *         )
     *       ),
     *       mediaType="application/json"
     *     )
     *   )
     * )
     * 
     * @param Request $request
     * @param Collection $collection
     * @return \Illuminate\Http\Response
     */
    public function listPlants(Request $request, Collection $collection)
    {
        if (!$request->input('per_page')) {
            $request->merge(['per_page' => 50]);
        }
        $plants = $collection->plants()->paginate($request->input('per_page'));
        $transformer = new \App\Fractal\Transformers\PlantTransformer();
        return $this->fractal->page($request, $plants, $transformer, 'plants');
    }
}
