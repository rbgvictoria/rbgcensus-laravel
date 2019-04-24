<?php

namespace App\Models;

/**
 * @property int $id
 * @property integer $plantId
 * @property string $createdAt
 * @property string $updatedAt
 * @property Plant $plant
 */
class Deaccession extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = ['plant_id', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plant()
    {
        return $this->belongsTo('App\Models\Plant');
    }
}
