<?php
// app/Models/RevenueRecord.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RevenueRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'teacher_id', 'student_id',
        'amount', 'commission', 'net_amount',
        'currency', 'status', 'paid_at',
    ];

    protected $casts = ['paid_at' => 'datetime'];

    public function course()  { return $this->belongsTo(Course::class); }
    public function teacher() { return $this->belongsTo(User::class, 'teacher_id'); }
    public function student() { return $this->belongsTo(User::class, 'student_id'); }

    // ── Scopes ──────────────────────────────────────────────
    public function scopeThisMonth($q) {
        return $q->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year);
    }
    public function scopeCompleted($q) { return $q->where('status', 'completed'); }

    // ── Accesseur montant formaté ────────────────────────────
    public function getNetAmountFormattedAttribute(): string {
        return number_format($this->net_amount, 0, ',', ' ') . ' ' . $this->currency;
    }
}