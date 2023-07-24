<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Writerprofile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nickname', 'principal_gender', 'description', 'birth_date', 'status'
    ];
}
