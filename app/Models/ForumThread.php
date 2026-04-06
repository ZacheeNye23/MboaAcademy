<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class ForumThread extends Model {
    use HasFactory;
    protected $fillable = ['course_id','user_id','title','body','is_pinned','is_closed','is_solved','views'];
    protected $casts    = ['is_pinned'=>'boolean','is_closed'=>'boolean','is_solved'=>'boolean'];
 
    public function course()    { return $this->belongsTo(Course::class); }
    public function author()    { return $this->belongsTo(User::class, 'user_id'); }
    public function replies()   { return $this->hasMany(ForumReply::class, 'thread_id'); }
    public function getRepliesCountAttribute(): int { return $this->replies()->count(); }
 
    public function incrementViews(): void { $this->increment('views'); }
}
 