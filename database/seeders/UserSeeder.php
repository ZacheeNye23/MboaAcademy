<?php

// database/seeders/UserSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ──────────────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'admin@mboacademy.com'],
            [
                'first_name' => 'Super',
                'last_name'  => 'Admin',
                'password'   => Hash::make('Admin@1234'),
                'role'       => User::ROLE_ADMIN,
                'country'    => 'CM',
                'is_active'  => true,
                'email_verified_at' => now(),
            ]
        );

        // ── Formateur ──────────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'formateur@mboacademy.com'],
            [
                'first_name' => 'Amara',
                'last_name'  => 'Diallo',
                'password'   => Hash::make('Teacher@1234'),
                'role'       => User::ROLE_TEACHER,
                'bio'        => 'Expert en Data Science et Machine Learning.',
                'country'    => 'SN',
                'is_active'  => true,
                'email_verified_at' => now(),
            ]
        );

        // ── Apprenant ──────────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'apprenant@mboacademy.com'],
            [
                'first_name' => 'Jean-Pierre',
                'last_name'  => 'Ngando',
                'password'   => Hash::make('Student@1234'),
                'role'       => User::ROLE_STUDENT,
                'country'    => 'CM',
                'is_active'  => true,
                'email_verified_at' => now(),
            ]
        );

        // ── Apprenants aléatoires (factory) ───────────────────────────────
        User::factory(10)->create(['role' => User::ROLE_STUDENT]);
        User::factory(3)->create(['role'  => User::ROLE_TEACHER]);

        $this->command->info('✅ Utilisateurs créés avec succès !');
    }
}