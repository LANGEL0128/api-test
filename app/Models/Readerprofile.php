<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Readerprofile extends Model
{
    use HasFactory;

    protected $fillable = [
        'favorite_gender',
        'reading_hours',
    ];
}
