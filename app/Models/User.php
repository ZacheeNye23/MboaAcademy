<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_STUDENT = 'student';
    const ROLE_TEACHER = 'teacher';
    const ROLE_ADMIN   = 'admin';

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password',
        'role', 'avatar', 'bio', 'phone', 'country', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    // ── Accesseurs ──────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : "https://ui-avatars.com/api/?name={$this->initials}&background=1a8a47&color=fff&bold=true";
    }

    // ── Helpers rôles ────────────────────────────────────────────────────────

    public function isStudent(): bool { return $this->role === self::ROLE_STUDENT; }
    public function isTeacher(): bool { return $this->role === self::ROLE_TEACHER; }
    public function isAdmin(): bool   { return $this->role === self::ROLE_ADMIN; }

    /**
     * ✅ Route du dashboard selon le rôle
     * Utilisée dans : navbar, auth controllers, middleware RedirectIfAuthenticated
     */
    public function dashboardRoute(): string
    {
        return match ($this->role) {
            self::ROLE_TEACHER => 'teacher.dashboard',
            self::ROLE_ADMIN   => 'admin.dashboard',
            default            => 'student.dashboard',
        };
    }

    // ── Relations ────────────────────────────────────────────────────────────

    public function courses()         { return $this->hasMany(Course::class); }
    public function enrollments()     { return $this->hasMany(Enrollment::class); }
    public function lessonProgress()  { return $this->hasMany(LessonProgress::class); }
    public function quizAttempts()    { return $this->hasMany(QuizAttempt::class); }
    public function certificates()    { return $this->hasMany(Certificate::class); }
    public function streak()          { return $this->hasOne(UserStreak::class); }
    public function forumThreads()    { return $this->hasMany(ForumThread::class); }
    public function forumReplies()    { return $this->hasMany(ForumReply::class); }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments')
                    ->withTimestamps()
                    ->withPivot(['enrolled_at', 'completed_at', 'progress_percent']);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
                    ->withPivot('earned_at')
                    ->withTimestamps();
    }
}