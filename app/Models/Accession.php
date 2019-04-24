<?php

namespace App\Models;

/**
 * @property integer $id
 * @property integer $provenanceTypeId
 * @property integer $identificationStatusId
 * @property integer $taxonId
 * @property string $createdAt
 * @property string $updatedAt
 * @property string $accessionNumber
 * @property string $collectorName
 * @property string $provenanceHistory
 * @property Taxa $taxon
 * @property ProvenanceType $provenanceType
 * @property IdentificationStatus $identificationStatus
 * @property Plant[] $plants
 */
class Accession extends BaseModel
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
    protected $fillable = ['provenance_type_id', 'identification_status_id', 'taxon_id', 'created_at', 'updated_at', 'accession_number', 'collector_name', 'provenance_history'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxon()
    {
        return $this->belongsTo('App\Models\Taxon', 'taxon_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provenanceType()
    {
        return $this->belongsTo('App\Models\ProvenanceType');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function identificationStatus()
    {
        return $this->belongsTo('App\Models\IdentificationStatus');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plants()
    {
        return $this->hasMany('App\Models\Plant');
    }
}
