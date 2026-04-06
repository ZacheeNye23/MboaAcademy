<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class QuizAttempt extends Model {
    use HasFactory;
    protected $fillable = [
        'user_id','quiz_id','score','total_points','earned_points',
        'passed','attempt_number','answers_given','time_spent','finished_at',
    ];
    protected $casts = [
        'passed'       => 'boolean',
        'answers_given'=> 'array',
        'finished_at'  => 'datetime',
    ];
 
    public function user() { return $this->belongsTo(User::class); }
    public function quiz() { return $this->belongsTo(Quiz::class); }
}
 