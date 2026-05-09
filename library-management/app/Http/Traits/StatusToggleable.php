<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\DB;

trait StatusToggleable 
{
    /**
     * Toggle status.
     */
    public static function toggleStatus($ids)
    {
        // Get table name
        $table = with(new self)->getTable();
        //---------------

        $active_status   = config('constants.statuses.ACTIVE.value') ?? 1;
        $inactive_status = config('constants.statuses.INACTIVE.value') ?? 0;

        $ids = implode(', ', $ids);

        return DB::statement(
            "UPDATE {$table} SET status = (CASE WHEN status = '{$active_status}' THEN '{$inactive_status}' ELSE '{$active_status}' END) WHERE id IN ({$ids})"
        );
    }
}
