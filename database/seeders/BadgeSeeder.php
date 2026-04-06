<?php
namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            ['name'=>'Premier pas',     'slug'=>'first-course',   'type'=>'first_course',   'description'=>'Avez commencé votre premier cours',   'required_value'=>1,  'color'=>'#25c26e'],
            ['name'=>'Diplômé',         'slug'=>'first-complete', 'type'=>'first_complete', 'description'=>'A terminé son premier cours',          'required_value'=>1,  'color'=>'#3b82f6'],
            ['name'=>'7 jours de feu',  'slug'=>'streak-7',       'type'=>'streak',         'description'=>'7 jours consécutifs d\'apprentissage', 'required_value'=>7,  'color'=>'#f97316'],
            ['name'=>'30 jours',        'slug'=>'streak-30',      'type'=>'streak',         'description'=>'30 jours consécutifs',                 'required_value'=>30, 'color'=>'#e8b84b'],
            ['name'=>'Quiz Master',     'slug'=>'quiz-master',    'type'=>'quiz_master',    'description'=>'Score parfait à un quiz',              'required_value'=>100,'color'=>'#a78bfa'],
            ['name'=>'Coder Jr',        'slug'=>'coder-jr',       'type'=>'completionist',  'description'=>'A terminé 3 cours de développement',   'required_value'=>3,  'color'=>'#25c26e'],
            ['name'=>'Contributeur',    'slug'=>'social',         'type'=>'social',         'description'=>'Premier post dans le forum',           'required_value'=>1,  'color'=>'#ec4899'],
            ['name'=>'Encyclopédie',    'slug'=>'fast-learner',   'type'=>'fast_learner',   'description'=>'A complété 50 leçons',                 'required_value'=>50, 'color'=>'#14b8a6'],
        ];

        foreach ($badges as $badge) {
            Badge::firstOrCreate(['slug' => $badge['slug']], $badge);
        }

        $this->command->info('✅ Badges créés !');
    }
}