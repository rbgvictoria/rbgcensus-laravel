<?php

namespace App\Models;

/**
 * @property integer $id
 * @property integer $createdAt
 * @property integer $updatedAt
 * @property string $name
 * @property string $abbreviation
 */
class PlantType extends BaseModel
{
    protected $table = 'plant_types';
}
