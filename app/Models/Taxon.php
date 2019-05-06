<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;

/**
 * @property integer $id
 * @property integer $rank_id
 * @property integer $parent_id
 * @property integer $accepted_id
 * @property integer $plant_type_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $taxon_name
 * @property string $author
 * @property string $vernacular_name
 * @property boolean $hide_from_public_display
 * @property int $lcd_species_id
 * @property int $node_number
 * @property int $highest_descendant_node_number
 * @property integer $depth
 * @property Rank $rank
 * @property Taxon $parent
 * @property Taxon $accepted
 * @property PlantType $plantType
 * @property Accession[] $accessions
 * @property Area[] $areas
 * 
 * @property Taxon[] $children
 * @property Taxon[] $higherClassification
 * @property Taxon $family
 * @property Classification $classification
 * 
 * @property boolean $isAustralianNative
 * @property boolean $isEndangered
 */
class Taxon extends Model
{
    use Eloquence, Mappable;
    
    protected $maps = [
        'naturalDistribution' => 'areas',
        'rankString' => 'rank.name',
    ];
    
    protected $appends = [
        'naturalDistribution',
    ];
    
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'taxa';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['rank_id', 'parent_id', 'accepted_id', 'plant_type_id', 'created_at', 'updated_at', 'taxon_name', 'author', 'vernacular_name', 'hide_from_public_display', 'lcd_species_id', 'node_number', 'highest_descendant_node_number', 'depth'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rank()
    {
        return $this->belongsTo('App\Models\Rank');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('App\Models\Taxon', 'parent_id');
    }
    
    /**
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function children()
    {
        return $this->hasMany('App\Models\Taxon', 'parent_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function accepted()
    {
        return $this->belongsTo('App\Models\Taxon', 'accepted_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plantType()
    {
        return $this->belongsTo('App\Models\PlantType');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accessions()
    {
        return $this->hasMany('App\Models\Accession', 'taxon_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function areas()
    {
        return $this->belongsToMany('App\Models\Area', 'taxon_areas', 'taxon_id');
    }
    
    /**
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function classification()
    {
        return $this->hasOne('App\Models\Classification');
    }
    
    /**
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getHigherClassificationAttribute()
    {
        return Taxon::where('node_number', '<', $this->node_number)
                ->where('highest_descendant_node_number', '>=', $this->node_number)
                ->orderBy('node_number')
                ->get();
    }
    
    /**
     * 
     * @return Taxon
     */
    public function getFamilyAttribute()
    {
        return Taxon::where('node_number', '<', $this->node_number)
                ->where('highest_descendant_node_number', '>=', $this->node_number)
                ->whereHas('rank', function($query) {
                    $query->where('ranks.name', 'family');
                })->first();
    }
    
    /**
     * 
     * @return boolean
     */
    public function getIsAustralianNativeAttribute()
    {
        if ($this->plantType && in_array($this->plantType->code, ['#', '&'])) {
            return true;
        }
        return false;
    }
    /**
     * 
     * @return boolean
     */
    public function getIsEndangeredAttribute()
    {
        if (in_array($this->plantType && $this->plantType->code, ['#', '!'])) {
            return true;
        }
        return false;
    }
}
