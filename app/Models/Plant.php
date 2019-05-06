<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

use App\PlantSearch\PlantSearch;


/**
 * @property integer $id
 * @property integer $accession_id
 * @property integer $grid_id
 * @property integer $bed_id
 * @property string $created_at
 * @property string $updated_at
 * @property int $plant_number
 * @property string $date_planted
 * 
 * @property Accession $accession
 * @property Grid $grid
 * @property Bed $bed
 * @property PlantAttribute[] $plantAttributes
 * @property Deaccession[] $deaccessions
 * @property Collection[] $collections
 * 
 * @property string $accessionNumber
 */
class Plant extends Model
{
    use Eloquence, Mappable;
    
    protected $maps = [
        'accessionNumber' => 'accession.accession_number',
        'taxonName' => 'accession.taxon.taxon_name',
        'gridCode' => 'grid.gridCode',
        'bedName' => 'bed.bedName',
        'site' => 'bed.location',
    ];
    
    protected $appends = [
        'accessionNumber',
        'taxonName',
        'gridCode',
        'bedName',
        'site',
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
    protected $fillable = ['accession_id', 'grid_id', 'bed_id', 'created_at', 'updated_at', 'plant_number', 'date_planted'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function accession()
    {
        return $this->belongsTo('App\Models\Accession');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grid()
    {
        return $this->belongsTo('App\Models\Grid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bed()
    {
        return $this->belongsTo('App\Models\Bed');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plantAttributes()
    {
        return $this->hasMany('App\Models\PlantAttribute');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deaccessions()
    {
        return $this->hasMany('App\Models\Deaccession');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function collections()
    {
        return $this->belongsToMany('App\Models\Collection', 'collection_plants');
    }
    
    public function search($root, array $args): Builder
    {
        $filter = isset($args['filter']) ? $args['filter'] : null;
        $sort = isset($args['sort']) ? $args['sort'] : 'taxonName';
        return PlantSearch::apply($filter, $sort);
    }

}
