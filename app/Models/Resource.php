<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Resource extends Model {
    use HasFactory;
    protected $fillable = ['lesson_id','title','file_path','url','type','file_size'];
 
    public function lesson() { return $this->belongsTo(Lesson::class); }
 
    public function getDownloadUrlAttribute(): string {
        return $this->file_path ? asset('storage/' . $this->file_path) : $this->url;
    }
 
    public function getFileSizeFormattedAttribute(): string {
        if (!$this->file_size) return '';
        $kb = $this->file_size / 1024;
        return $kb > 1024 ? round($kb/1024, 1).' MB' : round($kb, 0).' KB';
    }
}