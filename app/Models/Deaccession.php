<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property integer $plant_id
 * @property string $created_at
 * @property string $updated_at
 * @property Plant $plant
 */
class Deaccession extends Model
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
