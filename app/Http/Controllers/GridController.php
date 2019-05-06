<?php

namespace App\Http\Controllers;

use App\Models\Grid;
use App\Fractal\Transformers\GridTransformer;
use Illuminate\Http\Request;

class GridController extends ApiController
{
    /**
     * @OA\Get(
     *   path="/grids",
     *   summary="List grid cells",
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
     *           ref="#/components/schemas/Grid"
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
        $grids = Grid::paginate($request->input('per_page') ?: 50);
        return $this->fractal->page($request, $grids, new GridTransformer, 'grids');
    }
    
    /**
     * @OA\Get(
     *   path="/grids/{grid}",
     *   summary="Get single grid cell",
     *   @OA\Parameter(
     *     in="path",
     *     name="grid",
     *     description="ID of grid",
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
     *         ref="#/components/schemas/Grid"
     *       )
     *     )
     *   )
     * )
     * 
     * @param Grid $grid
     * @return \Illuminate\Http\Response
     */
    public function show(Grid $grid)
    {
        return $this->fractal->item($grid, new GridTransformer, 'grid');
    }
    
    /**
     * @OA\Get(
     *   path="/grids/{grid}/plants",
     *   summary="Get plants in grid cell",
     *   @OA\Parameter(
     *     in="path",
     *     name="grid",
     *     description="ID of grid cell",
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
     *       mediaType="application/json",
     *       @OA\Schema(
     *         type="array",
     *         @OA\Items(
     *           ref="#/components/schemas/Plant"
     *         )
     *       )
     *     )
     *   )
     * )
     * 
     * @param Request $request
     * @param Grid $grid
     * @return \Illuminate\Http\Response
     */
    public function listPlants(Request $request, Grid $grid)
    {
        if (!$request->input('per_page')) {
            $request->merge(['per_page' => 50]);
        }
        $plants = \App\Models\Plant::where('grid_id', $grid->id)
                ->paginate($request->input('per_page'));
        $transformer = new \App\Fractal\Transformers\PlantTransformer();
        return $this->fractal->page($request, $plants, $transformer, 'plants');
    }
}
