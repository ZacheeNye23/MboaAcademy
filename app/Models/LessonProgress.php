<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LessonProgress extends Model {
    use HasFactory;
    protected $fillable = ['user_id','lesson_id','is_completed','watch_time','completed_at'];
    protected $casts    = ['is_completed' => 'boolean', 'completed_at' => 'datetime'];
 
    public function user()   { return $this->belongsTo(User::class); }
    public function lesson() { return $this->belongsTo(Lesson::class); }
}