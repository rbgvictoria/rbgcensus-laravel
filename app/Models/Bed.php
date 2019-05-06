<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $parent_id
 * @property integer $bed_type_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $bed_name
 * @property string $bed_full_name
 * @property string $bed_code
 * @property boolean $is_restricted
 * @property string $location
 * @property int $node_number
 * @property int $highest_descendant_node_number
 * @property integer $depth
 * @property Bed $parent
 * @property BedType $bedType
 * @property Plant[] $plants
 * 
 * @property string $precinctName
 * @property string $subprecinctName
 */
class Bed extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['parent_id', 'bed_type_id', 'created_at', 'updated_at', 'bed_name', 'bed_code', 'precinct_name', 'is_restricted', 'location', 'precinct_code', 'subprecinct_code', 'subprecinct_name', 'node_number', 'highest_descendant_node_number', 'depth'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('App\Models\Bed', 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bed_type()
    {
        return $this->belongsTo('App\Models\BedType');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plants()
    {
        return $this->hasMany('App\Models\Plant');
    }

    /**
     *
     * @return Bed
     */
    public function getSiteAttribute()
    {
        return Bed::where('node_number', '<=', $this->node_number)
                ->where('highest_descendant_node_number', '>=', $this->node_number)
                ->whereHas('bed_type', function($query) {
                    $query->where('bed_types.name', 'location');
                })->first();
    }

    /**
     *
     * @return Bed
     */
    public function getPrecinctAttribute()
    {
        return Bed::where('node_number', '<=', $this->node_number)
                ->where('highest_descendant_node_number', '>=', $this->node_number)
                ->whereHas('bed_type', function($query) {
                    $query->where('bed_types.name', 'precinct');
                })->first();
    }

    /**
     *
     * @return Bed
     */
    public function getSubprecinctAttribute()
    {
        return Bed::where('node_number', '<=', $this->node_number)
                ->where('highest_descendant_node_number', '>=', $this->node_number)
                ->whereHas('bed_type', function($query) {
                    $query->where('bed_types.name', 'subprecinct');
                })->first();
    }


    
}
