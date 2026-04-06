<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class UserStreak extends Model {
    use HasFactory;
    protected $fillable = ['user_id','current_streak','longest_streak','last_activity_date'];
    protected $casts    = ['last_activity_date' => 'date'];
 
    public function user() { return $this->belongsTo(User::class); }
 
    /** Met à jour le streak après une activité d'apprentissage */
    public function updateStreak(): void {
        $today     = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
 
        if ($this->last_activity_date?->toDateString() === $today) return; // déjà compté
 
        if ($this->last_activity_date?->toDateString() === $yesterday) {
            $this->current_streak++;
        } else {
            $this->current_streak = 1; // reset si jour manqué
        }
 
        $this->longest_streak    = max($this->longest_streak, $this->current_streak);
        $this->last_activity_date = $today;
        $this->save();
    }
}
 