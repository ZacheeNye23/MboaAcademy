<?php
namespace App\Services;

use App\Models\Badge;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\User;
use App\Models\UserBadge;
use App\Models\UserStreak;

class BadgeService
{
    /**
     * Vérifie et attribue tous les badges mérités à un utilisateur.
     * À appeler après chaque action significative (leçon complétée, quiz, etc.)
     */
    public function checkAndAward(User $user): array
    {
        $awarded = [];

        $awarded = array_merge($awarded, $this->checkFirstCourse($user));
        $awarded = array_merge($awarded, $this->checkFirstComplete($user));
        $awarded = array_merge($awarded, $this->checkStreak($user));
        $awarded = array_merge($awarded, $this->checkLessonsCount($user));

        return $awarded;
    }

    /** Badge : premier cours commencé */
    private function checkFirstCourse(User $user): array
    {
        if (Enrollment::where('user_id', $user->id)->exists()) {
            return $this->award($user, 'first-course');
        }
        return [];
    }

    /** Badge : premier cours terminé */
    private function checkFirstComplete(User $user): array
    {
        if (Enrollment::where('user_id', $user->id)->whereNotNull('completed_at')->exists()) {
            return $this->award($user, 'first-complete');
        }
        return [];
    }

    /** Badge streak (7 jours, 30 jours) */
    private function checkStreak(User $user): array
    {
        $awarded = [];
        $streak  = UserStreak::where('user_id', $user->id)->first();
        if (!$streak) return [];

        if ($streak->current_streak >= 7)  $awarded = array_merge($awarded, $this->award($user, 'streak-7'));
        if ($streak->current_streak >= 30) $awarded = array_merge($awarded, $this->award($user, 'streak-30'));

        return $awarded;
    }

    /** Badge : 50 leçons complétées */
    private function checkLessonsCount(User $user): array
    {
        $count = LessonProgress::where('user_id', $user->id)->where('is_completed', true)->count();
        if ($count >= 50) return $this->award($user, 'fast-learner');
        return [];
    }

    /** Attribue un badge si pas déjà obtenu */
    private function award(User $user, string $slug): array
    {
        $badge = Badge::where('slug', $slug)->first();
        if (!$badge) return [];

        $existing = UserBadge::where('user_id', $user->id)->where('badge_id', $badge->id)->exists();
        if ($existing) return [];

        UserBadge::create(['user_id' => $user->id, 'badge_id' => $badge->id, 'earned_at' => now()]);

        return [['badge' => $badge->name, 'icon' => $badge->icon]];
    }
}