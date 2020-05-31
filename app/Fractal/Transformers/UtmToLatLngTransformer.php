<?php
/**
 * Copyright 2020 Royal Botanic Gardens Victoria
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Fractal\Transformers;

use League\Fractal\TransformerAbstract;

/**
 * Transforms converted Lat Longs to output
 * 
 * 
 * @OA\Schema(
 *   schema="UtmLatLng",
 *   type="object",
 *   example={"verbatimCoordinates": "MGA 56 306752 6190596", "verbatimSRS": "EPSG:28356", "verbatimCoordinateSystem": "UTM", "decimalLatitude": "-34.408037059371", "decimalLongitude": "150.897454603467", "geodeticDatum": "EPSG:4326"}
 * )

 */
class UtmToLatLngTransformer extends TransformerAbstract
{

  /**
   * @OA\property(
   *   property="verbatimCoordinates",
   *   type="string"
   * ),
   * @OA\property(
   *   property="verbatimSRS",
   *   type="string"
   * ),
   * @OA\property(
   *   property="verbatimCoordinateSystem",
   *   type="string"
   * ),
   * @OA\property(
   *   property="decimalLatitude",
   *   type="string",
   *   format="decimal",
   *   pattern="^-?\d{1,2}\.\d{10}$"
   * ),
   * @OA\property(
   *   property="decimalLongitude",
   *   type="string",
   *   format="decimal",
   *   pattern="^-?\d{1,3}\.\d{10}$"
   * ),
   * @OA\property(
   *   property="geodeticDatum",
   *   type="string"
   * )
   *
   * @param [type] $utmToLatLng
   * @return void
   */
  public function transform($utmToLatLng)
  {
    return [
      'verbatimCoordinates' => implode(' ', [
          $utmToLatLng->grid,
          $utmToLatLng->zone,
          $utmToLatLng->easting,
          $utmToLatLng->northing
        ]),
      'verbatimSRS' => 'EPSG:' . $utmToLatLng->gridsrs,
      'verbatimCoordinateSystem' => 'UTM',
      'decimalLatitude' => $utmToLatLng->lat,
      'decimalLongitude' => $utmToLatLng->lng,
      'geodeticDatum' => 'EPSG:' . $utmToLatLng->srs
    ];
  }
}

