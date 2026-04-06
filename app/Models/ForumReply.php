<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class ForumReply extends Model {
    use HasFactory;
    protected $fillable = ['thread_id','user_id','body','parent_id','is_solution'];
    protected $casts    = ['is_solution' => 'boolean'];
 
    public function thread()   { return $this->belongsTo(ForumThread::class); }
    public function author()   { return $this->belongsTo(User::class, 'user_id'); }
    public function parent()   { return $this->belongsTo(ForumReply::class, 'parent_id'); }
    public function children() { return $this->hasMany(ForumReply::class, 'parent_id'); }
}
