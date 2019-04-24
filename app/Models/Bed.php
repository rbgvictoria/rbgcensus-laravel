<?php

namespace App\Models;

use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;

/**
 * @property integer $id
 * @property integer $parentId
 * @property integer $bedTypeId
 * @property string $createdAt
 * @property string $updatedAt
 * @property string $bedName
 * @property string $bedCode
 * @property string $precinctName
 * @property boolean $isRestricted
 * @property string $location
 * @property int $precinctCode
 * @property int $subprecinctCode
 * @property string $subprecinctName
 * @property int $nodeNumber
 * @property int $highestDescendantNodeNumber
 * @property integer $depth
 * @property Bed $parent
 * @property BedType $bedType
 * @property Plant[] $plants
 */
class Bed extends BaseModel
{
    use Eloquence, Mappable;
    
    protected $maps = [
        'site' => 'location',
        'bedTypeString' => 'bed_type.name',
    ];
    
    protected $appends = [
        'site',
        'bedTypeString'
    ];
    
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
}
