<?php

namespace App\Models;

/**
 * @property integer $id 
 * @property datetime $createdAt 
 * @property datetime $updatedAt 
 * @property string $value 
 * 
 * @property Attribute $attribute
 */
class PlantAttribute extends BaseModel
{
    protected $table = 'plant_attributes';
    
    /**
     * @return \Illuminate\Database\Eloquent\BelongsTo
     */
    public function attribute()
    {
        return $this->belongsTo('App\Models\Attribute', 'attribute_id');
    }
}
