<?php

namespace App\Models;

/**
 * @property integer $id
 * @property datetime $createdAt
 * @property datetime $updatedAt
 * @property string $name
 * @property integer $sortOrder
 * 
 * @property Rank parent
 */
class Rank extends BaseModel
{
    protected $table = 'ranks';
    
    public function parent()
    {
        return $this->belongsTo('App\Models\Rank', 'parent_id');
    }
}
