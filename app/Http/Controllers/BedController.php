<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Fractal\Transformers\BedTransformer;
use Illuminate\Http\Request;

/**
 * 
 */
class BedController extends ApiController
{
    
    /**
     * @OA\Get(
     *   path="/beds",
     *   summary="List beds",
     *   @OA\Parameter(
     *     ref="#/components/parameters/include"
     *   ),
     *   @OA\Parameter(
     *     ref="#/components/parameters/per_page"
     *   ),
     *   @OA\Parameter(
     *     ref="#/components/parameters/page"
     *   ),
     *   @OA\Response(
     *     response="200",
     *     description="Successful response.",
     *     @OA\MediaType(
     *       @OA\Schema(
     *         type="array",
     *         @OA\Items(
     *           ref="#/components/schemas/Bed"
     *         )
     *       ),
     *       mediaType="application/json"
     *     )
     *   )
     * )
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $beds = Bed::paginate($request->input('per_page') ?: 50);
        return $this->fractal->page($request, $beds, new BedTransformer(), 'beds');
    }
    
    /**
     * @OA\Get(
     *   path="/beds/{bed}",
     *   summary="Get single bed",
     *   @OA\Parameter(
     *     in="path",
     *     name="bed",
     *     description="ID of bed",
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
     *       mediaType="application/json",
     *       @OA\Schema(
     *         ref="#/components/schemas/Bed"
     *       )
     *     )
     *   )
     * )
     * 
     * @param \App\Models\Bed $bed
     * @return \Illuminate\Http\Response
     */
    public function show(Bed $bed)
    {
        return $this->fractal->item($bed, new BedTransformer, 'bed');
    }
    
    /**
     * @OA\Get(
     *   path="/beds/{bed}/plants",
     *   summary="Get plants in bed",
     *   @OA\Parameter(
     *     in="path",
     *     name="bed",
     *     description="ID of bed",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Parameter(
     *     ref="#/components/parameters/include"
     *   ),
     *   @OA\Parameter(
     *     ref="#/components/parameters/per_page"
     *   ),
     *   @OA\Parameter(
     *     ref="#/components/parameters/page"
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
     *   ),
     * )
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Bed $bed
     * @return \Illuminate\Http\Response
     */
    public function listPlants(Request $request, Bed $bed)
    {
        if (!$request->input('per_page')) {
            $request->merge(['per_page' => 50]);
        }
        $plants = \App\Models\Plant::where('bed_id', $bed->id)
                ->paginate($request->input('per_page'));
        $transformer = new \App\Fractal\Transformers\PlantTransformer();
        return $this->fractal->page($request, $plants, $transformer, 'plants');
    }
}
