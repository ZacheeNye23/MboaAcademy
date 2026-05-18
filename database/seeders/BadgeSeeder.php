<?php
// database/seeders/BadgeSeeder.php
namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            ['name'=>'Premier pas',    'slug'=>'first-course',   'icon'=>'🎯','type'=>'first_course',  'description'=>'Commencé votre premier cours !',                'required_value'=>1,   'color'=>'#25c26e'],
            ['name'=>'Diplômé',        'slug'=>'first-complete', 'icon'=>'🎓','type'=>'first_complete','description'=>'Terminé votre premier cours !',                  'required_value'=>1,   'color'=>'#3b82f6'],
            ['name'=>'7 jours de feu', 'slug'=>'streak-7',       'icon'=>'🔥','type'=>'streak',        'description'=>'7 jours consécutifs d\'apprentissage !',         'required_value'=>7,   'color'=>'#f97316'],
            ['name'=>'Mois complet',   'slug'=>'streak-30',      'icon'=>'💪','type'=>'streak',        'description'=>'30 jours consécutifs ! Discipline exceptionnelle.','required_value'=>30,  'color'=>'#ef4444'],
            ['name'=>'Centenaire',     'slug'=>'streak-100',     'icon'=>'🦁','type'=>'streak',        'description'=>'100 jours consécutifs. Vous êtes une légende !', 'required_value'=>100, 'color'=>'#dc2626'],
            ['name'=>'Quiz Master',    'slug'=>'quiz-master',    'icon'=>'🧠','type'=>'quiz_master',   'description'=>'Score parfait 100% à un quiz !',                 'required_value'=>100, 'color'=>'#a78bfa'],
            ['name'=>'Assidu',         'slug'=>'lessons-10',     'icon'=>'📖','type'=>'fast_learner',  'description'=>'10 leçons complétées. Continuez ainsi !',        'required_value'=>10,  'color'=>'#14b8a6'],
            ['name'=>'Encyclopédie',   'slug'=>'lessons-50',     'icon'=>'📚','type'=>'fast_learner',  'description'=>'50 leçons complétées !',                         'required_value'=>50,  'color'=>'#0891b2'],
            ['name'=>'Insatiable',     'slug'=>'lessons-100',    'icon'=>'🌊','type'=>'fast_learner',  'description'=>'100 leçons ! Soif de connaissance sans limite.', 'required_value'=>100, 'color'=>'#1d4ed8'],
            ['name'=>'Coder Jr',       'slug'=>'dev-specialist', 'icon'=>'💻','type'=>'completionist', 'description'=>'3 cours de développement terminés !',            'required_value'=>3,   'color'=>'#059669'],
            ['name'=>'Contributeur',   'slug'=>'social-first',   'icon'=>'💬','type'=>'social',        'description'=>'Premier post dans le forum !',                   'required_value'=>1,   'color'=>'#ec4899'],
            ['name'=>'Ambassadeur',    'slug'=>'social-active',  'icon'=>'🌍','type'=>'social',        'description'=>'Acteur majeur de la communauté MboaAcademy.',   'required_value'=>10,  'color'=>'#7c3aed'],
            ['name'=>'Pionnier',       'slug'=>'pioneer',        'icon'=>'🚀','type'=>'custom',        'description'=>'Parmi les premiers inscrits sur MboaAcademy !',  'required_value'=>1,   'color'=>'#f59e0b'],
        ];

        foreach ($badges as $b) {
            Badge::firstOrCreate(['slug' => $b['slug']], $b);
        }

        $this->command->info('✅ ' . count($badges) . ' badges créés !');
    }
}