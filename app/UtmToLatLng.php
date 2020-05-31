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

namespace App;

use Illuminate\Support\Facades\DB;

class UtmToLatLng {

  public static function convert($grid, $zone, $easting, $northing, $outputdatum='WGS84')
  {
    $inputSRS = self::getInputSRS($grid, $zone);
    $outputSRS = self::getOutputSRS($outputdatum);
    $sql = "SELECT ST_X(ST_Transform(ST_GeomFromText('POINT($easting $northing)',$inputSRS),$outputSRS)) As lng,
            ST_Y(ST_Transform(ST_GeomFromText('POINT($easting $northing)',$inputSRS),$outputSRS)) As lat,
            '$outputSRS' AS srs,
            '$inputSRS' AS gridsrs,
            '$grid' AS grid,
            '$zone' AS \"zone\",
            '$easting' AS easting,
            '$northing' AS northing";
    return DB::select($sql);
  }

  private static function getInputSRS ($grid,$zone) {
    $prefix = array(
        'AMG' => '202',
        'AMG66' => '202',
        'AMG84' => '203',
        'MGA' => '283',
    );
    return $prefix[$grid] . $zone;
  }

  private static function getOutputSRS ($outputdatum) {
    $srs = array(
        'WGS84' => '4326',
        'GDA94' => '4283'
    );
    return $srs[$outputdatum];
  }


}