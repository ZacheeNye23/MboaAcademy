<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\ForumThread;
use Illuminate\Support\Facades\Broadcast;

/**
 * Canal de présence pour un thread du forum.
 * Retourne les données de l'utilisateur connecté (liste des membres en ligne).
 * Accessible aux : apprenants inscrits au cours ET formateurs du cours ET admins.
 */
Broadcast::channel('forum.thread.{threadId}', function ($user, int $threadId) {
    $thread = ForumThread::with('course')->find($threadId);
    if (!$thread) return false;

    $course = $thread->course;

    // Admin : accès total
    if ($user->isAdmin()) {
        return [
            'id'       => $user->id,
            'name'     => $user->full_name,
            'initials' => $user->initials,
            'role'     => $user->role,
        ];
    }

    // Formateur du cours
    if ($user->isTeacher() && $course->user_id === $user->id) {
        return [
            'id'       => $user->id,
            'name'     => $user->full_name,
            'initials' => $user->initials,
            'role'     => $user->role,
        ];
    }

    // Apprenant inscrit au cours
    if ($user->isStudent()) {
        $enrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($enrolled) {
            return [
                'id'       => $user->id,
                'name'     => $user->full_name,
                'initials' => $user->initials,
                'role'     => $user->role,
            ];
        }
    }

    return false; // Accès refusé
});