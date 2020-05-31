<?php

namespace App\Http\Controllers;

use App\Models\Accession;
use App\Fractal\Transformers\AccessionTransformer;
use Illuminate\Http\Request;

class AccessionController extends ApiController
{
    /**
     * @OA\Get(
     *   path="/accessions",
     *   summary="List accessions",
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
     *           ref="#/components/schemas/Accession"
     *         )
     *       )
     *     )
     *   )
     * )
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $accessions = Accession::paginate($request->input('per_page') ?: 50);
        return $this->fractal->page($request, $accessions, 
                new AccessionTransformer, 'accessions');
    }
    
    /**
     * @OA\Get(
     *   path="/accessions/{accession}",
     *   summary="Get single accession",
     *   @OA\Parameter(
     *     in="path",
     *     name="accession",
     *     description="ID of accession (this is not the same as the accession number)",
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
     *         ref="#/components/schemas/Accession"
     *       )
     *     )
     *   )
     * )
     * 
     * @param Accession $accession
     * @return \Illuminate\Http\Response
     */
    public function show(Accession $accession)
    {
        return $this->fractal->item($accession, new AccessionTransformer, 
                'accession');
    }
    
    /**
     * @OA\Get(
     *   path="/accessions/{accession}/plants",
     *   summary="Get plants from accession",
     *   @OA\Parameter(
     *     in="path",
     *     name="accession",
     *     description="ID of accession",
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
     * @param Accession $accession
     * @return \Illuminate\Http\Response
     */
    public function listPlants(Request $request, Accession $accession)
    {
        if (!$request->input('per_page')) {
            $request->merge(['per_page' => 50]);
        }
        $plants = $accession->plants()->paginate($request->input('per_page'));
        $transformer = new \App\Fractal\Transformers\PlantTransformer();
        return $this->fractal->page($request, $plants, $transformer, 'plants');
    }
}
