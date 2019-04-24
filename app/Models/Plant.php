<?php

namespace App\Models;

use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;

/**
 * @property integer $id
 * @property integer $accessionId
 * @property integer $gridId
 * @property integer $bedId
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $plantNumber
 * @property string $datePlanted
 * @property Accession $accession
 * @property Grid $grid
 * @property Bed $bed
 * @property PlantAttribute[] $plantAttributes
 * @property Deaccession[] $deaccessions
 * @property Collection[] $collections
 */
class Plant extends BaseModel
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
}
