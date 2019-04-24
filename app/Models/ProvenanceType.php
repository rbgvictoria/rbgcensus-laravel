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
class ProvenanceType extends BaseModel
{
    protected $table = 'provenance_types';
}
