<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Question extends Model {
    use HasFactory;
    protected $fillable = ['quiz_id','question','type','explanation','points','order'];
 
    public function quiz()    { return $this->belongsTo(Quiz::class); }
    public function answers() { return $this->hasMany(Answer::class)->orderBy('order'); }
 
    public function correctAnswers() { return $this->answers()->where('is_correct', true); }
}