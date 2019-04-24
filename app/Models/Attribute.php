<?php

namespace App\Models;

/**
 * @property integer $id 
 * @property datetime $createdAt 
 * @property datetime $updatedAt 
 * @property string name
 */
class Attribute extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'attributes;';
}
