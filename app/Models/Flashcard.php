<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flashcard extends Model
{
    use HasFactory;

    const NOT_ANSWERED = "not answered";
    const INCORRECT = "incorrect";
    const CORRECT = "correct";

    protected $fillable = [
        'question',
        'answer',
        'user_answer'
    ];

    public function scopePracticable($query)
    {
        $query->whereIn('user_answer', [Flashcard::NOT_ANSWERED, Flashcard::INCORRECT]);
    }

    public function scopeCorrect($query)
    {
        $query->where('user_answer', Flashcard::CORRECT);
    }

    public function scopeIncorrect($query)
    {
        $query->where('user_answer', Flashcard::INCORRECT);
    }

    public function scopeAnswered($query)
    {
        $query->whereIn('user_answer', [Flashcard::INCORRECT, Flashcard::CORRECT]);
    }
}
