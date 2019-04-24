<?php

namespace App\Models;

use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;

/**
 * @property integer $id
 * @property string $createdAt
 * @property string $updatedAt
 * @property string $code
 * @property string $geom
 * @property string $geomMga
 * @property Plant[] $plants
 */
class Grid extends BaseModel
{
    use Eloquence, Mappable;
    
    protected $maps = [
        'gridCode' => 'code',
    ];
    
    protected $appends = [
        'gridCode',
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
    protected $fillable = ['created_at', 'updated_at', 'code', 'geom', 'geom_mga'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plants()
    {
        return $this->hasMany('App\Models\Plant');
    }
}
