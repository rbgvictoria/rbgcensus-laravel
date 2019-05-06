<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $name
 * @property string $db_column_name
 * @property PlantAttribute[] $plantAttributes
 */
class Attribute extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['created_at', 'updated_at', 'name', 'db_column_name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plantAttributes()
    {
        return $this->hasMany('App\Models\PlantAttribute');
    }
}
