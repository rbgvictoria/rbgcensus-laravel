<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;

/**
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $code
 * @property string $geom
 * @property string $geom_mga
 * @property Plant[] $plants
 */
class Grid extends Model
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
