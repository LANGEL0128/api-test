<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookimage extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'photo',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
