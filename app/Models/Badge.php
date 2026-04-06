<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
 
class Badge extends Model {
    use HasFactory;
    protected $fillable = ['name','slug','description','color','type','required_value'];
 
    public function users() { return $this->belongsToMany(User::class, 'user_badges')->withPivot('earned_at')->withTimestamps(); }
}