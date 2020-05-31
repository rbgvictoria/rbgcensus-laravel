<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UtmToLatLng;
use App\Fractal\Transformers\UtmToLatLngTransformer;

class UtmConvertController extends ApiController
{
    /**
     * @OA\Get(
     *   path="/utmconvert",
     *   summary="Convert UTM coordinates to decimal latitude and longitude",
     *   @OA\Parameter(
     *     in="query",
     *     name="grid",
     *     description="UTM grid, allowed values are 'AMG', 'AMG66', 'AMG84' and 'MGA'. 'AMG' will be assumed to be AMG66",
     *     @OA\Schema(
     *       type="string",
     *       enum={"AMG","AMG66","AMG84","MGA"}
     *     )
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="zone",
     *     required=true,
     *     description="UTM zone, allowed values are between 49 and 56",
     *     @OA\Schema(
     *       type="integer",
     *       minimum=49,
     *       maximum=56
     *     )
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="easting",
     *     description="UTM easting in meters, maybe an integer or a decimal number with 6 digits (before the decimal point)",
     *     @OA\Schema(
     *       type="number",
     *       format="float"
     *     )
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="northing",
     *     description="UTM northing in meters, maybe an integer or a decimal number with 7 digits (before the decimal point)",
     *     @OA\Schema(
     *       type="number",
     *       format="float"
     *     )
     *   ),
     *   @OA\Response(
     *     response="200",
     *     description="Successful response.",
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         ref="#/components/schemas/UtmLatLng"
     *       )
     *     )
     *   )
     * ),
     * @OA\Post(
     *   path="/utmconvert",
     *   summary="Convert UTM coordinates to decimal latitude and longitude",
     *   @OA\RequestBody(
     *     request="UtmCoordinates",
     *     description="UTM coordinates to be converted to latitude and longitude",
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/UtmCoordinates"),
     *     @OA\MediaType(
     *       mediaType="application/x-www-form-urlencoded",
     *       @OA\Schema(
     *         ref="#/components/schemas/UtmCoordinates"
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response="200",
     *     description="Successful response.",
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         ref="#/components/schemas/UtmLatLng"
     *       )
     *     )
     *   )
     * )
     * 
     * @OA\Schema(
     *   schema="UtmCoordinates",
     *   type="object",
     *   @OA\Property(
     *     property="grid",
     *     description="UTM grid, allowed values are 'AMG', 'AMG66', 'AMG84' and 'MGA'. 'AMG' will be assumed to be AMG66",
     *     type="string",
     *     enum={"AMG","AMG66","AMG84","MGA"}
     *   ),
     *   @OA\Property(
     *     property="zone",
     *     description="UTM zone, allowed values are between 49 and 56",
     *     type="integer",
     *     minimum=49,
     *     maximum=56
     *   ),
     *   @OA\Property(
     *     property="easting",
     *     description="UTM easting in meters, maybe an integer or a decimal number with 6 digits (before the decimal point)",
     *     type="number",
     *     format="float"
     *   ),
     *   @OA\Property(
     *     property="northing",
     *     description="UTM northing in meters, maybe an integer or a decimal number with 7 digits (before the decimal point)",
     *     type="number",
     *     format="float"
     *   ),
     *   example={"grid": "MGA", "zone": 56, "easting": 306752, "northing": 6190596}
     * )
     * 
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function convert(Request $request)
    {
        $grid = $request->input('grid');
        $zone = $request->input('zone');
        $easting = $request->input('easting');
        $northing = $request->input('northing');

        $data = UtmToLatLng::convert($grid, $zone, $easting, $northing);

        return $this->fractal->item($data[0], new UtmToLatLngTransformer(), 'coordinates');

    }
}
