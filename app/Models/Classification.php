<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $taxon_id
 * @property string $kingdom
 * @property string $phylum
 * @property string $class
 * @property string $order
 * @property string $family
 * @property string $genus
 * @property string $species
 * @property string $subspecies
 * @property string $variety
 * @property string $form
 * @property string $cultivar
 * @property Taxa $taxa
 */
class Classification extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'classification';

    /**
     * @var array
     */
    protected $fillable = ['taxon_id', 'kingdom', 'phylum', 'class', 'order', 'family', 'genus', 'species', 'subspecies', 'variety', 'form', 'cultivar'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxon()
    {
        return $this->belongsTo('App\Models\Taxa', 'taxon_id');
    }
}
