<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property datetime $created_at
 * @property datetime $updated_at
 * @property string $name
 * @property string $abbreviation
 */
class BedType extends Model
{
    /**
     * @var string
     */
    protected $table = 'bed_types';
}
