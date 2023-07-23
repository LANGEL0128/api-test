<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'photo',
        'publication_date',
        'status',
    ];

    public function authors()
    {
        return $this->belongsToMany(Writerprofile::class, 'writerprofile_book', 'book_id', 'writerprofile_id');
    }
}
