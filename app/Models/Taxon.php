<?php

namespace App\Models;

use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;

/**
 * @property integer $id
 * @property integer $rankId
 * @property integer $parentId
 * @property integer $acceptedId
 * @property integer $plantTypeId
 * @property string $createdAt
 * @property string $updatedAt
 * @property string $taxonName
 * @property string $author
 * @property string $vernacularName
 * @property boolean $hideFromPublicDisplay
 * @property int $lcdSpeciesId
 * @property int $nodeNumber
 * @property int $highestDescendantNodeNumber
 * @property integer $depth
 * @property Rank $rank
 * @property Taxon $parent
 * @property Taxon $accepted
 * @property PlantType $plantType
 * @property Accession[] $accessions
 * @property Area[] $areas
 */
class Taxon extends BaseModel
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
}
