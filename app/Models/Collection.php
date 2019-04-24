<?php

namespace App\Models;

/**
 * @property integer $id 
 * @property datetime $createdAt 
 * @property datetime $updatedAt 
 * @property string $name
 */
class Collection extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'collections';
}
