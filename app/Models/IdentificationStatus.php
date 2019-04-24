<?php

namespace App\Models;

/**
 * @property integer $id
 * @property datetime $createdAt
 * @property datetime $updatedAt
 * @property string $code
 * @property string $label
 * @property string $description
 */
class IdentificationStatus extends BaseModel
{
    protected $table = 'identification_statuses';
}
