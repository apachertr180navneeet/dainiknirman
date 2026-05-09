<?php

namespace App\Http\Traits;

trait Orderable 
{
    /**
     * Scope a query to order the records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $order
     * @param  string  $column
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query, $order = 'ASC', $column = 'order')
    {
        return $query->orderBy($column, $order);
    }
}
