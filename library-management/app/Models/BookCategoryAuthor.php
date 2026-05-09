<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookCategoryAuthor extends Model
{
    protected $table = "book_category_authors";
    protected $fillable = [
        'book_id',
        'author_id',
        'created_by',
        'updated_by'
    ];

    public function book(){
        return $this->belongsTo(Book::class, 'book_id', 'id');
    }

    public function author(){
        return $this->belongsTo(User::class, 'author_id', 'id');
    }
}
