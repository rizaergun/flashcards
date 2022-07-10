<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flashcard extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'user_answer'
    ];

    public function scopePracticable($query)
    {
        $query->whereIn('user_answer', ['not answered', 'incorrect']);
    }
}
