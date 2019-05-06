<?php

namespace App\Http\Controllers;

use App\Fractal\FractalManager;
use App\Models\Plant;
use App\PlantSearch\PlantSearch;
use App\Fractal\Transformers\PlantTransformer;
use Illuminate\Http\Request;

class PlantController extends ApiController
{
    /**
     *
     * @var FractalManager
     */
    protected $fractal;
    
    /**
     * @OA\Get(
     *   path="/plants",
     *   summary="Search for plant records",
     *   @OA\Parameter(
     *     in="query",
     *     name="filter",
     *     style="deepObject",
     *     description="Terms the output should be filtered on. Query should be formatted in deepObject style, e.g. 'filter[taxon_name]=Acacia&filter[site]=Melbourne'. Available filter terms can be found in the PlantFilter Schema.",
     *     @OA\Schema(
     *       ref="#/components/schemas/PlantFilter"
     *     )
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="sort",
     *     @OA\Schema(
     *       type="string",
     *       enum={"taxon_name","family","bed_name","-taxon_name","-family","-bed_name"}
     *     ),
     *     description="Field the result should be ordered by. Allowed values are 'taxon_name', 'family' and 'bed_name'; prepend with '-' for descending order"
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
     *   @OA\Parameter(
     *     ref="#/components/parameters/exclude"
     *   ),
     *   @OA\Response(
     *     response="200",
     *     description="Successful response.",
     *     @OA\MediaType(
     *       @OA\Schema(
     *         type="array",
     *         @OA\Items(
     *           ref="#/components/schemas/Plant"
     *         )
     *       ),
     *       mediaType="application/json"
     *     )
     *   )
     * ),
     * @OA\Schema(
     *   schema="PlantFilter",
     *   description="Filters for plant search",
     *   type="object",
     *   @OA\Property(
     *     property="taxonName",
     *     type="string",
     *     description="Search by taxon name. Taxon name searches are case-insensitive; a wild card is automatically added at the end of the search string."
     *   ),
     *   @OA\Property(
     *     property="family",
     *     type="string",
     *     description="Search by family. Case-sensitive exact search."
     *   ),
     *   @OA\Property(
     *     property="site",
     *     type="string",
     *     description="Search by site. Case-sensitive exact search."
     *   ),
     *   @OA\Property(
     *     property="bedName",
     *     type="string",
     *     description="Search by bed name. Case-sensitive exact search."
     *   ),
     *   @OA\Property(
     *     property="precinct",
     *     type="string",
     *     description="Search by precinct. Case-sensitive exact search. Only Cranbourne Gardens has precincts"
     *   ),
     *   @OA\Property(
     *     property="subprecinct",
     *     type="string",
     *     description="Search by subprecinct. Case-sensitive exact search. Only Cranbourne Gardens has subprecincts"
     *   ),
     *   @OA\Property(
     *     property="naturalDistributionArea",
     *     type="string",
     *     description="Search by natural distribution area, for example 'China', 'Australia'. Case-insensitive wild card search."
     *   )
     * )
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $builder = PlantSearch::apply($request->input('filter'), $request->input('sort'));
        $plants = $builder->paginate($request->input('per_page') ?: 50);
        $transformer = new PlantTransformer();
        return $this->fractal->page($request, $plants, $transformer, 'plants');
    }
    
    /**
     * @OA\Get(
     *   path="/plants/{id}",
     *   summary="Get a single Plant record",
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     @OA\Schema(
     *       type="integer",
     *       format="int64"
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
     *       ref="#/components/schemas/Plant"
     *       ),
     *       mediaType="application/json"
     *     )
     *   ),
     * )
     * 
     * @param Plant $plant
     * @return \Illuminate\Http\Response;
     */
    public function show(Plant $plant)
    {
        $transformer = new PlantTransformer();
        return $this->fractal->item($plant, $transformer, 'plant');
    }
}
