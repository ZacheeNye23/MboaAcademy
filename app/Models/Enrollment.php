<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
 
class Enrollment extends Model {
    use HasFactory;
    protected $fillable = ['user_id','course_id','enrolled_at','completed_at','progress_percent'];
    protected $casts    = ['enrolled_at' => 'datetime', 'completed_at' => 'datetime'];
 
    public function user()   { return $this->belongsTo(User::class); }
    public function course() { return $this->belongsTo(Course::class); }
 
    public function isCompleted(): bool { return !is_null($this->completed_at); }
 
    /**
     * Recalcule et met à jour le % de progression en BDD
     */
    public function recalculateProgress(): int {
        $totalLessons     = $this->course->lessons()->count();
        if ($totalLessons === 0) return 0;
 
        $completedLessons = LessonProgress::where('user_id', $this->user_id)
            ->whereIn('lesson_id', $this->course->lessons()->pluck('id'))
            ->where('is_completed', true)
            ->count();
 
        $percent = (int) round(($completedLessons / $totalLessons) * 100);
        $this->update([
            'progress_percent' => $percent,
            'completed_at'     => $percent === 100 ? now() : null,
        ]);
        return $percent;
    }
}