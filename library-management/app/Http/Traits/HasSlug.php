<?php

namespace App\Http\Traits;

trait HasSlug 
{
    /**
     * Scope a query to include record using slug.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string                                 $slug
     * @param  string                                 $column
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSlug($query, $slug, $column = 'slug')
    {
        return $query->where($column, $slug);
    }
}
