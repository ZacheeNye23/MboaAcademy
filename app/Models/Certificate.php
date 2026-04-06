<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Certificate extends Model {
    use HasFactory;
    protected $fillable = ['user_id','course_id','certificate_number','file_path','issued_at'];
    protected $casts    = ['issued_at' => 'datetime'];
 
    public function user()   { return $this->belongsTo(User::class); }
    public function course() { return $this->belongsTo(Course::class); }
 
    public function getDownloadUrlAttribute(): string {
        return $this->file_path ? asset('storage/' . $this->file_path) : '#';
    }
 
    /** Génère un numéro unique ex: MBOA-2024-00042 */
    public static function generateNumber(): string {
        $count = static::count() + 1;
        return 'MBOA-' . now()->year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}
