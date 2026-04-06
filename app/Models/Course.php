<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'slug', 'description', 'what_you_learn',
        'thumbnail', 'preview_video', 'status', 'level', 'language',
        'category', 'price', 'is_free', 'duration_minutes',
    ];

    protected $casts = ['is_free' => 'boolean'];

    protected static function boot() {
        parent::boot();
        static::creating(fn($c) => $c->slug ??= Str::slug($c->title) . '-' . Str::random(5));
    }

    // Relations
    public function teacher()      { return $this->belongsTo(User::class, 'user_id'); }
    public function chapters()     { return $this->hasMany(Chapter::class)->orderBy('order'); }
    public function lessons()      { return $this->hasManyThrough(Lesson::class, Chapter::class); }
    public function enrollments()  { return $this->hasMany(Enrollment::class); }
    public function students()     { return $this->belongsToMany(User::class, 'enrollments')->withTimestamps(); }
    public function quizzes()      { return $this->hasMany(Quiz::class); }
    public function forumThreads() { return $this->hasMany(ForumThread::class); }
    public function certificates() { return $this->hasMany(Certificate::class); }
    public function reviews()      { return $this->hasMany(CourseReview::class); }
    public function revenues()     { return $this->hasMany(RevenueRecord::class); }

    // Accesseurs
    public function getThumbnailUrlAttribute(): string {
        return $this->thumbnail ? asset('storage/'.$this->thumbnail) : asset('images/course-default.jpg');
    }
    public function getTotalLessonsAttribute(): int   { return $this->lessons()->count(); }
    public function getEnrollmentsCountAttribute(): int { return $this->enrollments()->count(); }
    public function getAverageRatingAttribute(): float  { return round($this->reviews()->avg('rating') ?? 0, 1); }
    public function getDurationFormattedAttribute(): string {
        $h = intdiv($this->duration_minutes, 60); $m = $this->duration_minutes % 60;
        return $h > 0 ? "{$h}h {$m}min" : "{$m}min";
    }
    public function getStatusLabelAttribute(): string {
        return match($this->status) { 'published'=>'Publié','pending'=>'En révision','draft'=>'Brouillon','rejected'=>'Refusé', default=>$this->status };
    }
    public function getStatusColorAttribute(): string {
        return match($this->status) { 'published'=>'#25c26e','pending'=>'#e8b84b','draft'=>'rgba(255,255,255,0.35)','rejected'=>'#f87171', default=>'#fff' };
    }

    // Scopes
    public function scopePublished($q)      { return $q->where('status', 'published'); }
    public function scopeFree($q)           { return $q->where('is_free', true); }
    public function scopeByTeacher($q, $id) { return $q->where('user_id', $id); }
}