<?php

namespace App\Http\GraphQL\Directives;

use Illuminate\Support\Facades\DB;
use Nuwave\Lighthouse\Schema\Values\ArgumentValue;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Support\Contracts\ArgMiddleware;
use Nuwave\Lighthouse\Support\Traits\HandlesQueryFilter;
use function GuzzleHttp\json_encode;

class SearchFilterDirective extends BaseDirective implements ArgMiddleware
{
    use HandlesQueryFilter;

    /**
     * Name of the directive.
     *
     * @return string
     */
    public function name(): string
    {
        return 'search';
    }

    /**
     * Resolve the field directive.
     *
     * @param ArgumentValue $argument
     * @param \Closure       $next
     *
     * @return ArgumentValue
     */
    public function handleArgument(ArgumentValue $argument, \Closure $next): ArgumentValue
    {
        $this->injectFilter(
            $argument,
            function ($query, string $columnName, $value) {
                if (!$this->isJoined($query, 'accessions')) {
                    $query->join('accessions', 'plants.accession_id', '=', 'accessions.id')
                            ->join('taxa', 'accessions.taxon_id', '=', 'taxa.id')
                            ->join('beds', 'plants.bed_id', '=', 'beds.id')
                            ->join('bed_types', 'beds.bed_type_id', '=', 'bed_types.id');                            ;
                }

                if ($columnName === 'taxonName') {
                    $query->where('taxa.taxon_name', 'LIKE', "$value%");
                }

                if ($columnName === 'family') {
                    $query->join('classification', 'taxa.id', '=', 'classification.taxon_id')
                            ->where('classification.family', $value);
                }

                if ($columnName === 'site') {
                    $site = DB::table('beds')
                            ->join('bed_types', 'beds.bed_type_id', '=', 'bed_types.id')
                            ->where('bed_types.name', 'location')
                            ->where('beds.bed_name', $value)
                            ->select('beds.node_number', 'highest_descendant_node_number')
                            ->first();
                    $nodeNumber = $site ? $site->node_number : 0;
                    $highestDescendantNodeNumber = $site ? $site->highest_descendant_node_number : 0;

                    $query->where('beds.node_number', '>=', $nodeNumber)
                            ->where('beds.node_number', '<=', $highestDescendantNodeNumber);
                }

                if ($columnName === 'precinct') {
                    $precinct = DB::table('beds')
                            ->join('bed_types', 'beds.bed_type_id', '=', 'bed_types.id')
                            ->where('bed_types.name', 'precinct')
                            ->where('beds.bed_name', $value)
                            ->select('beds.node_number', 'highest_descendant_node_number')
                            ->first();
                    $nodeNumber = $precinct ? $precinct->node_number : 0;
                    $highestDescendantNodeNumber = $precinct ? $precinct->highest_descendant_node_number : 0;
                    $query->where('beds.node_number', '>=', $nodeNumber)
                            ->where('beds.node_number', '<=', $highestDescendantNodeNumber);
                }

                if ($columnName === 'subprecinct') {
                    $subprecinct = DB::table('beds')
                            ->join('bed_types', 'beds.bed_type_id', '=', 'bed_types.id')
                            ->where('bed_types.name', 'subprecinct')
                            ->where('beds.bed_name', $value)
                            ->select('beds.node_number', 'highest_descendant_node_number')
                            ->first();
                    $nodeNumber = $subprecinct ? $subprecinct->node_number : 0;
                    $highestDescendantNodeNumber = $subprecinct ? $subprecinct->highest_descendant_node_number : 0;
                    $query->where('beds.node_number', '>=', $nodeNumber)
                            ->where('beds.node_number', '<=', $highestDescendantNodeNumber);
                }

                if ($columnName === 'bed') {
                    $query->where('bed_types.name', 'bed')
                            ->where('beds.bed_name', 'LIKE', "%$value%");
                }

                if ($columnName === 'gridCode') {
                    $query->join('grids', 'plants.grid_id', '=', 'grids.id')
                            ->where('grids.code', '=', $value);
                }

                if ($columnName === 'naturalDistributionArea') {
                    $query->join('taxon_areas', 'taxa.id', '=', 'taxon_areas.taxon_id')
                            ->join('areas', 'taxon_areas.area_id', '=', 'areas.id')
                            ->where('areas.area_full_name', 'LIKE', "%$value%");
                }

                return $query;
            }
        );

        return $next($argument);
    }

    protected function isJoined($query, $table)
    {
        $joins = $query->getQuery()->joins;
        if($joins == null) {
            return false;
        }
        foreach ($joins as $join) {
            if ($join->table == $table) {
                return true;
            }
        }
        return false;
    }
}
