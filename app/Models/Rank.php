<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $parent_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $name
 * @property int $sort_order
 * @property Rank $parent
 * @property Taxon[] $taxa
 */
class Rank extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['parent_id', 'created_at', 'updated_at', 'name', 'sort_order'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('App\Models\Rank', 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxa()
    {
        return $this->hasMany('App\Models\Taxon');
    }
}
