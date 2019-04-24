<?php

/*
 * Copyright 2019 Royal Botanic Gardens Victoria.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Description of UpdateAccessionsTableService
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class UpdateAccessionsTableService 
{
    public function update()
    {
        $select = "SELECT CONCAT('RBGM ', SUBSTRING(AccessionNo, 3)) as accession_number, group_concat(distinct IDStatus) AS id_status, 
              group_concat(distinct ProvenanceTypeCode) AS provenance_type_code,
              group_concat(DISTINCT ProvenanceHistoryCode) AS provenance_history, group_concat(distinct CollectorName) AS collector_name,
              group_concat(distinct species) AS species, cast(group_concat(distinct SpeciesID) as unsigned) AS species_id
            FROM mysql_plantlist
            GROUP BY AccessionNo
            UNION
            SELECT CONCAT('RBGC ', SUBSTRING(AccessionNo, 3)), group_concat(distinct IDStatus), group_concat(distinct ProvenanceTypeCode),
              group_concat(DISTINCT ProvenanceHistoryCode), group_concat(distinct CollectorName),
              group_concat(distinct species), cast(group_concat(distinct SpeciesID) as unsigned)
            FROM mysql_plantlist_rbgc
            GROUP BY AccessionNo";
        $accessions = DB::connection('mysql')->select(DB::raw($select));
        if ($accessions) {
            foreach ($accessions as $row) {
                $provenanceTypeId = $this->getProvenanceTypeCodeId($row->provenance_type_code);
                $identificationStatusId = $this->getIdentificationStatusId($row->id_status);
                
                $row->provenance_type_code = 
                        (in_array($row->provenance_type_code, ['W', 'Z'])) ? 
                        $row->provenance_type_code : NULL;
                $taxonId = DB::table('taxa')
                        ->where('lcd_species_id', $row->species_id)
                        ->value('id');
                $r = DB::table('accessions as a')
                        ->join('taxa as t', 'a.taxon_id', '=', 't.id')
                        ->where('a.accession_number', $row->accession_number)
                        ->select('a.id', 'a.accession_number', 
                                'a.provenance_type_id', 'a.provenance_history', 
                                'a.collector_name', 'a.identification_status_id', 
                                't.lcd_species_id')
                        ->first();
                if ($r) {
                    if ((($identificationStatusId || $r->identification_status) && $identificationStatusId != $r->identification_status) ||
                            (($provenanceTypeId || $r->provenance_type_code) && $provenanceTypeId != $r->provenance_type_code) ||
                            (($row->provenance_history || $r->provenance_history) && $row->provenance_history != $r->provenance_history) ||
                            (($row->collector_name || $r->collector_name) && $row->collector_name != $r->collector_name) ||
                            ($row->species_id != $r->lcd_species_id)) {
                        DB::table('accessions')->where('id', $r->id)->update([
                            'updated_at' => DB::raw('NOW()'),
                            'accession_number' => $row->accession_number,
                            'provenance_type_id' => $provenanceTypeId,
                            'provenance_history' => $row->provenance_history,
                            'collector_name' => $row->collector_name,
                            'identification_status_id' => $identificationStatusId,
                            'taxon_id' => $taxonId,
                        ]);
                    }                  
                }
                else {
                    DB::table('accessions')->insert([
                        'created_at' => DB::raw('NOW()'),
                        'updated_at' => DB::raw('NOW()'),
                        'accession_number' => $row->accession_number,
                        'provenance_type_id' => $provenanceTypeId,
                        'provenance_history' => $row->provenance_history,
                        'collector_name' => $row->collector_name,
                        'identification_status_id' => $identificationStatusId,
                        'taxon_id' => $taxonId,
                    ]);
                }
            }
        }
    }
    
    protected function getProvenanceTypeCodeId($code) 
    {
        return DB::table('provenance_types')->where('code', $code)->value('id');
    }
    
    protected function getIdentificationStatusId($code)
    {
        return DB::table('identification_statuses')->where('code', $code)->value('id');
    }
}
