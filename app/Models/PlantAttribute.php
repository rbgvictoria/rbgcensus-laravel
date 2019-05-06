<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property integer $plant_id
 * @property integer $attribute_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $value
 * @property Plant $plant
 * @property Attribute $attribute
 */
class PlantAttribute extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['plant_id', 'attribute_id', 'created_at', 'updated_at', 'value'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plant()
    {
        return $this->belongsTo('App\Models\Plant');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attribute()
    {
        return $this->belongsTo('App\Models\Attribute');
    }
}
