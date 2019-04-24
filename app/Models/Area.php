<?php

namespace App\Models;

/**
 * @property integer $id
 * @property datetime $createdAt
 * @property datetime $updatedAt
 * @property string $localityId
 * @property string $areaName
 * @property string $areaFullName
 * @property string $isoCode
 * @property integer $nodeNumber
 * @property integer $highestDescendantNodeNumber
 * @property integer $depth
 */
class Area extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'areas';
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('App\Models\Area', 'parent_id');
    }
}
