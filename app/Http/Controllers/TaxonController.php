<?php

namespace App\Http\Controllers;

use App\Models\Taxon;
use App\Fractal\Transformers\TaxonTransformer;
use Illuminate\Http\Request;

class TaxonController extends ApiController
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Taxon  $taxon
     * @return \Illuminate\Http\Response
     */
    public function show(Taxon $taxon)
    {
        return $this->fractal->item($taxon, new TaxonTransformer, 'taxon');
    }
    
    public function listAccessions(Request $request, $id)
    {
        if (!$request->input('per_page')) {
            $request->merge(['per_page' => 50]);
        }
        $accessions = \App\Models\Accession::where('taxon_id', $id)
                ->paginate($request->input('per_page'));
        $transformer = new \App\Fractal\Transformers\AccessionTransformer;
        return $this->fractal->page($request, $accessions, $transformer,'accessions');
    }

}
