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
 * Description of UpdateTaxaTable
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class UpdateTaxaTableService extends UpdateTableService {
    
    protected $nodeNumber;
    
    public function update()
    {
        $select = "SELECT s.Species AS taxon_name, REPLACE(s.Authorship, ')', ') ') AS author, s.Genus AS genus, s.Family as family,
                REPLACE(s.InfraAuthor1, ')', ') ') AS InfraAuthor1, REPLACE(s.InfraAuthor2, ')', ') ') AS InfraAuthor2,
                s.InfraEpithet1, s.InfraEpithet2,
                coalesce(spl.CommonName, splc.CommonName) AS common_name,
                coalesce(spl.PlantType, splc.PlantType) AS plant_type,
                coalesce(spl.IsRestricted, splc.IsRestricted) AS is_restricted,
                s.SpeciesID AS species_id
            FROM mysql_species s
            LEFT JOIN (
                SELECT SpeciesID, GROUP_CONCAT(DISTINCT PlantType) AS PlantType, GROUP_CONCAT(DISTINCT CommonName) AS CommonName,
                  CAST(FLOOR(SUM(ABS(blnNoPublicDisplay))/count(SpeciesID)) AS unsigned) AS IsRestricted
                FROM mysql_plantlist
                GROUP BY SpeciesID) AS spl ON s.SpeciesID=spl.SpeciesID
            LEFT JOIN (
                SELECT SpeciesID, GROUP_CONCAT(DISTINCT PlantType) AS PlantType, GROUP_CONCAT(DISTINCT CommonName) AS CommonName,
                  CAST(FLOOR(SUM(ABS(blnNoPublicDisplay))/count(SpeciesID)) AS unsigned) AS IsRestricted
                FROM mysql_plantlist_rbgc
                GROUP BY SpeciesID) AS splc ON s.SpeciesID=splc.SpeciesID
            WHERE (spl.SpeciesID IS NOT NULL OR splc.SpeciesID IS NOT NULL)";
        $result = DB::connection('mysql')->select(DB::raw($select));
        if ($result) {
            foreach ($result as $row) {
                $row->author = str_replace(array(')', '.&', '.ex ', ','), array(') ', '. &', '. ex ', ', '), $row->author);
                $row->author = str_replace('  ', ' ', $row->author);
                $row->author = str_replace('  ', ' ', $row->author);
                $row->InfraAuthor1 = str_replace(array(')', '.&', '.ex ', ','), array(') ', '. &', '. ex ', ', '), $row->InfraAuthor1);
                $row->InfraAuthor1 = str_replace('  ', ' ', $row->InfraAuthor1);
                $row->InfraAuthor2 = str_replace(array(')', '.&', '.ex ', ','), array(') ', '. &', '. ex ', ', '), $row->InfraAuthor2);
                $row->InfraAuthor2 = str_replace('  ', ' ', $row->InfraAuthor2);
                
                $row->common_name = $this->trimFromEndOfLine($row->common_name);
                
                $sciname = $row->taxon_name;
                if ($row->InfraEpithet2) {
                    $namebits = explode(' ', $row->taxon_name);
                    $ranks = array();
                    foreach (array('subsp.', 'var.', 'f.') as $rank) {
                        $key = array_search($rank, $namebits);
                        if ($key !== FALSE) {
                            $ranks[] = $key;
                        }
                    }
                    if (count($ranks) > 1) {
                        $sciname = array();
                        for ($i = 0; $i < $ranks[0]; $i++) {
                            $sciname[] = $namebits[$i];
                        }
                        for ($i = $ranks[1]; $i < count($namebits); $i++) {
                            $sciname[] = $namebits[$i];
                        }
                        $sciname = implode(' ', $sciname);
                    }
                }
                
                $author = NULL;
                if (!(strpos($sciname, "'") !== FALSE || strpos($sciname, "(") !== FALSE || 
                        strpos($sciname, ' sp.') !== FALSE || strpos($sciname, ' sp ') !== FALSE ||
                        strpos($sciname, '?') !== FALSE)) {
                    if ($row->InfraEpithet2) {
                        $author = $row->InfraAuthor2;
                    }
                    elseif ($row->InfraEpithet1) {
                        $author = $row->InfraAuthor1;
                    }
                    else {
                        $author = $row->author;
                    }
                }
                
                $rank = 'species';
                if (strpos($sciname, ' subsp. ') !== false) {
                    $rank = 'subspecies';
                }
                if (strpos($sciname, ' var. ') !== false) {
                    $rank = 'variety';
                }
                if (strpos($sciname, ' f. ') !== false) {
                    $rank = 'form';
                }
                
                $isCultivar = false;
                if (strpos($sciname, "'") !== false) {
                    $isCultivar = true;
                }
                
                // clean up hybrid names and formulas
                if (substr($sciname, 0, 2) === 'x ') {
                    $sciname = '×' . substr($sciname, 2);
                }
                $sciname = preg_replace('/^x([A-Z])/', '×$1', $sciname);
                if (preg_match('/^×/', $sciname)) {
                    $row->genus = '×' . $row->genus;
                }
                
                if (strpos($sciname, ' x ') !== false) {
                    if (strpos($sciname, ' ', strpos($sciname, ' x ')+3) !== false && !$isCultivar) {
                        $sciname = str_replace(' x ', ' × ', $sciname);
                    }
                    else {
                        $sciname = str_replace(' x ', ' ×', $sciname);
                    }
                }
                
                if ($isCultivar) {
                    $parentName = trim(substr($sciname, 0, strpos($sciname, "'")));
                    if (strpos($parentName, ' × ') !== false) {
                        $isCultivar = false;
                    }
                }
                
                if (!$isCultivar) {
                    if ($rank === 'species') {
                        $genusId = DB::table('taxa')->where('taxon_name', $row->genus)->value('id');
                        $id = DB::table('taxa')->where('lcd_species_id', $row->species_id)->value('id');

                        if ($id) {
                            DB::table('taxa')
                                    ->where('id', $id)
                                    ->update([
                                'updated_at' => DB::raw('NOW()'),
                                'parent_id' => $genusId,
                                'taxon_name' => $sciname,
                                'rank_id' => $this->getRankId($rank),
                                'author' => $author,
                                'vernacular_name' => $row->common_name,
                                'plant_type_id' => $this->getPlantTypeId($row->plant_type),
                                'hide_from_public_display' => $row->is_restricted
                            ]);
                        }
                        else {
                            if (!$genusId) {
                                $parentId = DB::table('taxa')->where('taxon_name', $row->family)->value('id');
                                $genusId = DB::table('taxa')->insertGetId([
                                    'created_at' => DB::raw('NOW()'),
                                    'updated_at' => DB::raw('NOW()'),
                                    'parent_id' => $parentId,
                                    'taxon_name' => $row->genus,
                                    'rank_id' => $this->getRankId('genus'),
                                ]);
                            }

                            DB::table('taxa')->insert([
                                'created_at' => DB::raw('NOW()'),
                                'updated_at' => DB::raw('NOW()'),
                                'parent_id' => $genusId,
                                'taxon_name' => $sciname,
                                'rank_id' => $this->getRankId($rank),
                                'author' => $author,
                                'vernacular_name' => $row->common_name,
                                'lcd_species_id' => $row->species_id,
                                'plant_type_id' => $this->getPlantTypeId($row->plant_type),
                                'hide_from_public_display' => $row->is_restricted
                            ]);
                        }
                    }
                    else { //infraspecific taxa
                        $nameBits = explode(' ', $sciname);
                        $speciesName = implode(' ', array_splice($nameBits, 0, 2));
                        $id = DB::table('taxa')->where('lcd_species_id', $row->species_id)->value('id');
                        if ($id) {
                            DB::table('taxa')
                                    ->where('id', $id)
                                    ->update([
                                'updated_at' => DB::raw('NOW()'),
                                'taxon_name' => $sciname,
                                'rank_id' => $this->getRankId($rank),
                                'author' => $author,
                                'vernacular_name' => $row->common_name,
                                'plant_type_id' => $this->getPlantTypeId($row->plant_type),
                                'hide_from_public_display' => $row->is_restricted
                            ]);
                        }
                        else {
                            $speciesId = DB::table('taxa')->where('taxon_name', $speciesName)->value('id');
                            if (!$speciesId) {
                                $genusId = DB::table('taxa')->where('taxon_name', $row->genus)->value('id');
                                if (!$genusId) {
                                    $parentId = DB::table('taxa')->where('taxon_name', $row->family)->value('id');
                                    $genusId = DB::table('taxa')->insertGetId([
                                        'created_at' => DB::raw('NOW()'),
                                        'updated_at' => DB::raw('NOW()'),
                                        'parent_id' => $parentId,
                                        'taxon_name' => $row->genus,
                                        'rank_id' => $this->getRankId('genus'),
                                    ]);
                                }
                                $speciesId = DB::table('taxa')->insertGetId([
                                    'created_at' => DB::raw('NOW()'),
                                    'updated_at' => DB::raw('NOW()'),
                                    'parent_id' => $genusId,
                                    'taxon_name' => $speciesName,
                                    'rank_id' => $this->getRankId('species'),
                                ]);
                            }
                            
                            DB::table('taxa')->insert([
                                'created_at' => DB::raw('NOW()'),
                                'updated_at' => DB::raw('NOW()'),
                                'parent_id' => $speciesId,
                                'taxon_name' => $sciname,
                                'rank_id' => $this->getRankId($rank),
                                'author' => $author,
                                'vernacular_name' => $row->common_name,
                                'lcd_species_id' => $row->species_id,
                                'plant_type_id' => $this->getPlantTypeId($row->plant_type),
                                'hide_from_public_display' => $row->is_restricted
                            ]);
                        }
                    }
                }
                else {
                    $id = DB::table('taxa')->where('lcd_species_id', $row->species_id)->value('id');
                    if ($id) {
                        DB::table('taxa')
                                ->where('id', $id)
                                ->update([
                            'updated_at' => DB::raw('NOW()'),
                            'taxon_name' => $sciname,
                            'rank_id' => $this->getRankId($rank),
                            'author' => $author,
                            'vernacular_name' => $row->common_name,
                            'plant_type_id' => $this->getPlantTypeId($row->plant_type),
                            'hide_from_public_display' => $row->is_restricted
                        ]);
                    }
                    else {
                        if ($rank === 'species') {
                            $parentId = DB::table('taxa')->where('taxon_name', $parentName)->value('id');
                            if (!$parentId) {
                                $genusId = DB::table('taxa')
                                        ->where('taxon_name', $row->genus)
                                        ->where('rank_id', $this->getRankId('genus'))
                                        ->value('id');
                                if (!$genusId) {
                                    $familyId = DB::table('taxa')
                                            ->where('taxon_name', $row->family)
                                            ->where('rank_id', $this->getRankId('family'))
                                            ->value('id');
                                    $genusId = DB::table('taxa')->insertGetId([
                                        'created_at' => DB::raw('NOW()'),
                                        'updated_at' => DB::raw('NOW()'),
                                        'parent_id' => $familyId,
                                        'taxon_name' => $row->genus,
                                        'rank_id' => $this->getRankId('genus'),
                                    ]);
                                }
                                if ($parentName === $row->genus) {
                                    $parentId = $genusId;
                                }
                                else {
                                    $parentId = DB::table('taxa')->insertGetId([
                                        'created_at' => DB::raw('NOW()'),
                                        'updated_at' => DB::raw('NOW()'),
                                        'parent_id' => $genusId,
                                        'taxon_name' => $parentName,
                                        'author' => $author,
                                        'rank_id' => $this->getRankId('species'),
                                    ]);
                                }
                            }
                            DB::table('taxa')->insert([
                                'created_at' => DB::raw('NOW()'),
                                'updated_at' => DB::raw('NOW()'),
                                'parent_id' => $parentId,
                                'taxon_name' => $sciname,
                                'rank_id' => $this->getRankId('cultivar'),
                                'vernacular_name' => $row->common_name,
                                'lcd_species_id' => $row->species_id,
                                'plant_type_id' => $this->getPlantTypeId($row->plant_type),
                                'hide_from_public_display' => $row->is_restricted
                            ]);
                        }
                        else { // cultivars of infraspecific rank
                            $parentId = DB::table('taxa')
                                    ->where('taxon_name', $parentName)
                                    ->value('id');
                            if (!$parentId) {
                                $parentNameBits = explode(' ', $parentName);
                                $speciesName = implode(' ', array_splice($parentNameBits, 0, 2));
                                $speciesId = DB::table('taxa')->where('taxon_name', $speciesName)->value('id');
                                if (!$speciesId) {
                                    $genusId = DB::table('taxa')->where('taxon_name', $row->genus)->value('id');
                                    if (!$genusId) {
                                        $familyId = DB::table('taxa')->where('taxon_name', $row->family)->value('id');
                                        $genusId = DB::table('taxa')->insertGetId([
                                            'created_at' => DB::raw('NOW()'),
                                            'updated_at' => DB::raw('NOW()'),
                                            'parent_id' => $familyId,
                                            'taxon_name' => $row->genus,
                                            'rank_id' => $this->getRankId('genus'),
                                        ]);
                                    }
                                    $speciesId = DB::table('taxa')->insertGetId([
                                        'created_at' => DB::raw('NOW()'),
                                        'updated_at' => DB::raw('NOW()'),
                                        'parent_id' => $genusId,
                                        'taxon_name' => $speciesName,
                                        'rank_id' => $this->getRankId('species'),
                                    ]);
                                }
                                $parentId = DB::table('taxa')->insertGetId([
                                    'created_at' => DB::raw('NOW()'),
                                    'updated_at' => DB::raw('NOW()'),
                                    'parent_id' => $speciesId,
                                    'taxon_name' => $parentName,
                                    'rank_id' => $this->getRankId($rank),
                                ]);
                            }
                            DB::table('taxa')->insert([
                                'created_at' => DB::raw('NOW()'),
                                'updated_at' => DB::raw('NOW()'),
                                'parent_id' => $parentId,
                                'taxon_name' => $sciname,
                                'rank_id' => $this->getRankId('cultivar'),
                                'vernacular_name' => $row->common_name,
                                'lcd_species_id' => $row->species_id,
                                'plant_type_id' => $this->getPlantTypeId($row->plant_type),
                                'hide_from_public_display' => $row->is_restricted
                            ]);
                        }
                    }
                }
            }
        }
        
    }
    
    public function nestedSets()
    {
        $this->nodeNumber = 0;
        
        $roots = DB::table('taxa as t')
                ->join('ranks as r', 't.rank_id', '=', 'r.id')
                ->whereNull('t.parent_id')
                ->orderBy('r.sort_order')
                ->orderBy('t.taxon_name')
                ->pluck('t.id');
        
        foreach ($roots as $id) {
            $this->createNode($id, 0);
        }
    }
    
    protected function createNode($parentId, $depth)
    {
        $this->nodeNumber++;
        DB::table('taxa')->where('id', $parentId)->update([
            'node_number' => $this->nodeNumber,
            'depth' => $depth,
        ]);
        $children = DB::table('taxa as t')
                ->join('ranks as r', 't.rank_id', '=', 'r.id')
                ->where('t.parent_id', $parentId)
                ->orderBy('r.sort_order')
                ->orderBy('t.taxon_name')
                ->pluck('t.id');
        if ($children) {
            foreach ($children as $id) {
                $this->createNode($id, $depth + 1);
            }
        }
        DB::table('taxa')->where('id', $parentId)->update([
            'highest_descendant_node_number' => $this->nodeNumber,
        ]);
    }
    
    public function synonyms()
    {
        $select = "SELECT synonym, group_concat(newname) as new_name
            FROM mysql_synonyms m
            WHERE synonym REGEXP '^[A-Z]' AND synonym!=newname
            GROUP BY synonym
            HAVING count(*)=1";
        $result = DB::connection('mysql')->select(DB::raw($select));
        if ($result) {
            foreach ($result as $row) {
                $acceptedId = DB::table('taxa')
                        ->where('taxon_name', $row->new_name)->value('id');
                if ($acceptedId) {
                    $synonymId = DB::table('taxa')
                            ->where('taxon_name', $row->synonym)->value('id');
                    $rank = 'species';
                    if (strpos($row->synonym, ' subsp. ') !== false) {
                        $rank = 'subspecies';
                    }
                    if (strpos($row->synonym, ' var. ') !== false) {
                        $rank = 'variety';
                    }
                    if (strpos($row->synonym, ' f. ') !== false) {
                        $rank = 'form';
                    }
                    if (strpos($row->synonym, "'") !== false) {
                        $rank = 'cultivar';
                    }
                    if ($synonymId) {
                        DB::table('taxa')->where('id', $synonymId)->update([
                            'updated_at' => DB::raw('NOW()'),
                            'parent_id' => null,
                            'accepted_id' => $acceptedId,
                        ]);
                        DB::table('taxa')->where('accepted_id', $synonymId)
                                ->update([
                                    'accepted_id' => $acceptedId,
                                ]);
                    }
                    else {
                        DB::table('taxa')->insert([
                            'created_at' => DB::raw('NOW()'),
                            'updated_at' => DB::raw('NOW()'),
                            'taxon_name' => $row->synonym,
                            'accepted_id' => $acceptedId,
                            'rank_id' => $this->getRankId($rank),
                        ]);
                    }
                }
            }
        }
    }
    
    protected function trimFromEndOfLine($str)
    {
        $bits = preg_split('/\r|\n/', $str);
        return $bits[0];
    }
    
    public function makeClassification()
    {
        DB::table('classification')->delete();
        
        $taxa = DB::table('taxa')
                ->whereNotNull('node_number')
                ->select('id', 'node_number', 'highest_descendant_node_number')
                ->get();
        foreach ($taxa as $taxon) {
            $ranks = DB::table('taxa')
                    ->join('ranks', 'taxa.rank_id', '=', 'ranks.id')
                    ->where('taxa.node_number', '<=', $taxon->node_number)
                    ->where('taxa.highest_descendant_node_number', '>=', $taxon->node_number)
                    ->select('ranks.name as rank', 'taxa.taxon_name')
                    ->get();
            $insertArray = ['taxon_id' => $taxon->id];
            foreach ($ranks as $rank) {
                $insertArray[$rank->rank] = $rank->taxon_name;
            }
            if (isset($insertArray['kingdom'])) {
                DB::table('classification')->insert($insertArray);
            }
        }
    }
    
}
