<?php

namespace App\Models;

/**
 * @property integer $id
 * @property datetime $createdAt
 * @property datetime $updatedAt
 * @property string $name
 * @property string $abbreviation
 */
class BedType extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'bed_types';
}
