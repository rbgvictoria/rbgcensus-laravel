<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $code
 * @property string $label
 * @property string $description
 * @property Accession[] $accessions
 */
class ProvenanceType extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['created_at', 'updated_at', 'code', 'label', 'description'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accessions()
    {
        return $this->hasMany('App\Models\Accession');
    }
}
