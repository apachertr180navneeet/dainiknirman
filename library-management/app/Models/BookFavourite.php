<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookFavourite extends Model
{
    protected $table = "book_favourite";
    protected $fillable = [
        'user_id',
        'book_id',
        'created_by',
        'updated_by'
    ];

    public function book(){
        return $this->belongsTo(Book::class, 'book_id', 'id');
    }
}
