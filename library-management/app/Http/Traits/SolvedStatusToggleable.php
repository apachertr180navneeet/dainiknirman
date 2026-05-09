<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\DB;

trait SolvedStatusToggleable 
{
    /**
     * Toggle status.
     */
    public static function toggleSolvedStatus($ids)
    {
        // Get table name
        $table = with(new self)->getTable();
        //---------------

        $active_status   = config('constants.statuses.ACTIVE.value');
        $inactive_status = config('constants.statuses.INACTIVE.value');

        $ids = implode(', ', $ids);

        return DB::statement(
            DB::raw(
                "UPDATE {$table} SET is_solved = (CASE WHEN is_solved = CAST('{$active_status}' AS BOOLEAN) THEN CAST('{$inactive_status}' AS BOOLEAN) ELSE CAST('{$active_status}' AS BOOLEAN) END) WHERE id IN ({$ids})"
            )
        );
    }
}