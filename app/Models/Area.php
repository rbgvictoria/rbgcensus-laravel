<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property datetime $created_at
 * @property datetime $updated_at
 * @property string $locality_id
 * @property string $area_name
 * @property string $area_full_name
 * @property string $iso_code
 * @property integer $node_number
 * @property integer $highest_descendant_node_number
 * @property integer $depth
 * @property Area $parent
 */
class Area extends Model
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
