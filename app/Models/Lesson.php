<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Lesson extends Model {
    use HasFactory;
    protected $fillable = [
        'chapter_id','title','content','video_path',
        'video_url','duration','order','is_free','type',
    ];
    protected $casts = ['is_free' => 'boolean'];
 
    public function chapter()   { return $this->belongsTo(Chapter::class); }
    public function course()    { return $this->hasOneThrough(Course::class, Chapter::class, 'id', 'id', 'chapter_id', 'course_id'); }
    public function resources() { return $this->hasMany(Resource::class); }
    public function quizzes()   { return $this->hasMany(Quiz::class); }
    public function progress()  { return $this->hasMany(LessonProgress::class); }
 
    public function getDurationFormattedAttribute(): string {
        if (!$this->duration) return '—';
        $m = intdiv($this->duration, 60);
        $s = $this->duration % 60;
        return $m > 0 ? "{$m}min {$s}s" : "{$s}s";
    }
}
 
