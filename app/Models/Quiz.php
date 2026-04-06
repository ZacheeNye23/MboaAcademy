<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Quiz extends Model {
    use HasFactory;
    protected $fillable = [
        'course_id','lesson_id','title','description',
        'passing_score','max_attempts','duration_minutes','show_answers',
    ];
    protected $casts = ['show_answers' => 'boolean'];
 
    public function course()    { return $this->belongsTo(Course::class); }
    public function lesson()    { return $this->belongsTo(Lesson::class); }
    public function questions() { return $this->hasMany(Question::class)->orderBy('order'); }
    public function attempts()  { return $this->hasMany(QuizAttempt::class); }
 
    public function attemptsForUser(int $userId) {
        return $this->attempts()->where('user_id', $userId);
    }
 
    public function canAttempt(int $userId): bool {
        return $this->attemptsForUser($userId)->count() < $this->max_attempts;
    }
 
    public function bestScoreForUser(int $userId): ?int {
        return $this->attemptsForUser($userId)->max('score');
    }
}
 
