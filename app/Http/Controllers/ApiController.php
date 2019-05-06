<?php

namespace App\Http\Controllers;

use App\Fractal\FractalManager;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * The ApiController class is the superclass of all API controllers; it contains
 * some methods that are used in all these controllers
 *
 * @OA\OpenApi(
 *   @OA\Info(
 *     title="RBGV Living Collections Census API",
 *     description="",
 *     version="1.0.0",
 *     @OA\Contact(
 *       name="Niels Klazenga, Royal Botanic Gardens Victoria",
 *       email="Niels.Klazenga@rbg.vic.gov.au"
 *     )
 *   ),
 *   @OA\Server(
 *     description="",
 *     url="http://rbgcensus-laravel.test/api"
 *   ),
 *   @OA\Components(
 *     @OA\Parameter(
 *       in="path",
 *       name="id",
 *       @OA\Schema(
 *         type="integer",
 *         format="int64"
 *       )
 *     ),
 *     @OA\Parameter(
 *       in="query",
 *       name="per_page",
 *       @OA\Schema(
 *         type="integer",
 *         default=50
 *       ),
 *       description="Number of results to return per page."
 *     ),
 *     @OA\Parameter(
 *       in="query",
 *       name="page",
 *       @OA\Schema(
 *         type="integer",
 *         default=1
 *       ),
 *       description="Page to return."
 *     ),
 *     @OA\Parameter(
 *       in="query",
 *       name="include",
 *       @OA\Schema(
 *         type="array",
 *         @OA\Items(
 *           type="string"
 *         ),
 *         collectionFormat="csv"
 *       ),
 *       style="simple",
 *       description="Extra linked resources to include in the result; linked resources within included resources can be appended, separated by a full stop, e.g. 'accession.taxon'; multiple resources can be included, separated by a comma."
 *     ),
 *     @OA\Parameter(
 *       in="query",
 *       name="exclude",
 *       @OA\Schema(
 *         type="array",
 *         @OA\Items(
 *           type="string"
 *         ),
 *         collectionFormat="csv"
 *       ),
 *       description="Embedded resources to exclude from the result; format as for the 'include' parameter."
 *     ),
 *   )
 * )
 */
class ApiController extends Controller
{
    protected $fractal;
    
    public function __construct() 
    {
        $this->fractal = new FractalManager();
    }
    
    /**
     * Creates API documentation
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function apiDocs()
    {
        $swagger = \OpenApi\scan(app_path());
        return response()->json($swagger);
    }
    
    public function apiYaml()
    {
        $swagger = \OpenApi\scan(app_path());
        return response($swagger->toYaml(), 200, 
                ['Content-type' => 'application/x-yaml']);
    }
}
