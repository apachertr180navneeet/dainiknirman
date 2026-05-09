<?php

namespace App\Http\Traits;

trait Statusable 
{
    /**
     * Scope a query to only include active records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query, $column = 'status')
    {
        // return $query->where($column, config('constants.statuses.ACTIVE.value'));
        return $query->where($column, 1);
    }

    /**
     * Scope a query to only include inactive records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query, $column = 'status')
    {
        // return $query->where('status', config('constants.statuses.INACTIVE.value'));
        return $query->where($column, 0);
    }
}
