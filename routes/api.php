<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', function() {
    return view('swagger');
});
Route::get('/swagger.json', 'ApiController@apiDocs');
Route::get('/swagger.yaml', 'ApiController@apiYaml');

Route::get('/taxa/{taxon}', 'TaxonController@show')->name('api.taxon.show');
Route::get('/taxa/{taxon}/accessions', 'TaxonController@listAccessions')->name('api.taxon.accessions.list');

Route::get('/accessions/{accession}', 'AccessionController@show')->name('api.accession.show');

Route::get('/beds', 'BedController@index')->name('api.beds.list');
Route::get('/beds/{bed}', 'BedController@show')->name('api.bed.show');
Route::get('/beds/{bed}/plants', 'BedController@listPlants')->name('api.bed.plants.list');

Route::get('/grids/{grid}', 'GridController@show')->name('api.grid.show');
Route::get('/grids/{grid}/plants', 'GridController@listPlants')->name('api.grid.plants.list');

Route::get('/plants', 'PlantController@index')->name('api.plants.list');
Route::get('/plants/{plant}', 'PlantController@show')->name('api.plant.show');

Route::get('/collections', 'CollectionController@index')->name('api.collections.list');
Route::get('/collections/{collection}', 'CollectionController@show')->name('api.collections.show');
Route::get('/collections/{collection}/plants', 'CollectionController@listPlants')->name('api.collection.plants.list');

Route::match(['get', 'post'], '/utmconvert', 'UtmConvertController@convert')->name('api.utmconvert.convert');
Route::post('/utmBatchConvert', 'UtmConvertController@batchConvert')->name('api.utmconvert.batchConvert');
