<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\UserStreak;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Créer les formateurs ──────────────────────────────────────────────
        $teacher1 = User::firstOrCreate(
            ['email' => 'teacher1@mboaacademy.cm'],
            [
                'first_name' => 'Jean',
                'last_name'  => 'Mbarga',
                'password'   => Hash::make('password'),
                'role'       => 'teacher',
                'country'    => 'CM',
                'email_verified_at' => now(),
            ]
        );

        $teacher2 = User::firstOrCreate(
            ['email' => 'teacher2@mboaacademy.cm'],
            [
                'first_name' => 'Fatima',
                'last_name'  => 'Bello',
                'password'   => Hash::make('password'),
                'role'       => 'teacher',
                'country'    => 'CM',
                'email_verified_at' => now(),
            ]
        );

        foreach ([$teacher1, $teacher2] as $t) {
            UserStreak::firstOrCreate(
                ['user_id' => $t->id],
                ['current_streak' => 0, 'longest_streak' => 0]
            );
        }

        $coursesData = [

            [
                'course' => [
                    'user_id'          => $teacher1->id,
                    'title'            => 'Laravel 11 — Développement Web Complet',
                    'description'      => 'Maîtrisez Laravel 11 de zéro à expert. Ce cours complet vous apprendra à construire des applications web professionnelles avec le framework PHP le plus populaire. De la configuration de l\'environnement jusqu\'au déploiement en production, vous couvrirez tous les aspects essentiels du développement moderne.',
                    'what_you_learn'   => "• Installer et configurer Laravel 11\n• Créer des modèles, migrations et contrôleurs\n• Maîtriser Eloquent ORM\n• Construire des APIs REST complètes\n• Authentification avec Laravel Breeze & Sanctum\n• Déployer en production",
                    'category'         => 'Développement web',
                    'level'            => 'beginner',
                    'language'         => 'fr',
                    'price'            => 15000,
                    'is_free'          => false,
                    'status'           => 'published',
                    'duration_minutes' => 480,
                ],
                'chapters' => [
                    [
                        'title' => 'Introduction & Installation',
                        'lessons' => [
                            ['title' => 'Présentation du cours et de Laravel', 'type' => 'video', 'duration' => 480, 'is_free' => true, 'video_url' => null],
                            ['title' => 'Installation de l\'environnement (WAMP/Laragon)', 'type' => 'video', 'duration' => 720, 'is_free' => true, 'video_url' => null],
                            ['title' => 'Créer votre premier projet Laravel', 'type' => 'video', 'duration' => 600, 'is_free' => false, 'video_url' => null],
                        ],
                    ],
                    [
                        'title' => 'Routing & Contrôleurs',
                        'lessons' => [
                            ['title' => 'Les routes dans Laravel', 'type' => 'video', 'duration' => 900, 'is_free' => false, 'video_url' => null],
                            ['title' => 'Créer et organiser les contrôleurs', 'type' => 'video', 'duration' => 840, 'is_free' => false, 'video_url' => null],
                            ['title' => 'Middleware et groupes de routes', 'type' => 'text', 'duration' => 300, 'is_free' => false, 'video_url' => null, 'content' => "Les middlewares permettent de filtrer les requêtes HTTP.\n\n## Créer un middleware\n```bash\nphp artisan make:middleware CheckAge\n```"],
                        ],
                    ],
                    [
                        'title' => 'Eloquent ORM & Base de données',
                        'lessons' => [
                            ['title' => 'Migrations et structure de la BD', 'type' => 'video', 'duration' => 1020, 'is_free' => false, 'video_url' => null],
                            ['title' => 'Les modèles Eloquent', 'type' => 'video', 'duration' => 960, 'is_free' => false, 'video_url' => null],
                            ['title' => 'Relations : hasMany, belongsTo, manyToMany', 'type' => 'video', 'duration' => 1200, 'is_free' => false, 'video_url' => null],
                        ],
                    ],
                    [
                        'title' => 'Authentification & Sécurité',
                        'lessons' => [
                            ['title' => 'Laravel Breeze — Auth complète en 5 minutes', 'type' => 'video', 'duration' => 780, 'is_free' => false, 'video_url' => null],
                            ['title' => 'API Authentication avec Sanctum', 'type' => 'video', 'duration' => 1080, 'is_free' => false, 'video_url' => null],
                        ],
                    ],
                ],
            ],

            [
                'course' => [
                    'user_id'          => $teacher1->id,
                    'title'            => 'Python pour la Data Science',
                    'description'      => 'Apprenez Python et ses bibliothèques essentielles pour analyser des données et créer des visualisations percutantes. Ce cours vous donnera les bases solides pour débuter en Data Science avec des projets concrets adaptés au contexte africain.',
                    'what_you_learn'   => "• Bases de Python (variables, boucles, fonctions)\n• Manipulation de données avec Pandas\n• Visualisation avec Matplotlib et Seaborn\n• Introduction au Machine Learning avec Scikit-learn\n• Projets pratiques sur des données réelles",
                    'category'         => 'Data Science',
                    'level'            => 'beginner',
                    'language'         => 'fr',
                    'price'            => 0,
                    'is_free'          => true,
                    'status'           => 'published',
                    'duration_minutes' => 360,
                ],
                'chapters' => [
                    [
                        'title' => 'Introduction à Python',
                        'lessons' => [
                            ['title' => 'Pourquoi Python pour la Data Science ?', 'type' => 'video', 'duration' => 540, 'is_free' => true, 'video_url' => null],
                            ['title' => 'Installation de Python et Jupyter Notebook', 'type' => 'video', 'duration' => 660, 'is_free' => true, 'video_url' => null],
                            ['title' => 'Variables, types et opérateurs', 'type' => 'video', 'duration' => 780, 'is_free' => true, 'video_url' => null],
                        ],
                    ],
                    [
                        'title' => 'Structures de données Python',
                        'lessons' => [
                            ['title' => 'Listes et tuples', 'type' => 'video', 'duration' => 720, 'is_free' => true, 'video_url' => null],
                            ['title' => 'Dictionnaires et sets', 'type' => 'video', 'duration' => 660, 'is_free' => true, 'video_url' => null],
                            ['title' => 'Fonctions et modules', 'type' => 'text', 'duration' => 480, 'is_free' => true, 'video_url' => null, 'content' => "## Les fonctions en Python\n\n```python\ndef calculer_moyenne(notes):\n    return sum(notes) / len(notes)\n\nnotes = [14, 16, 12, 18, 15]\nprint(f'Moyenne: {calculer_moyenne(notes)}')\n```"],
                        ],
                    ],
                    [
                        'title' => 'Pandas — Manipulation de données',
                        'lessons' => [
                            ['title' => 'Introduction à Pandas et DataFrames', 'type' => 'video', 'duration' => 900, 'is_free' => true, 'video_url' => null],
                            ['title' => 'Filtrer, trier et grouper des données', 'type' => 'video', 'duration' => 1020, 'is_free' => true, 'video_url' => null],
                            ['title' => 'Nettoyage de données — valeurs manquantes', 'type' => 'video', 'duration' => 840, 'is_free' => true, 'video_url' => null],
                        ],
                    ],
                ],
            ],

            [
                'course' => [
                    'user_id'          => $teacher2->id,
                    'title'            => 'Design UI/UX avec Figma — De zéro à pro',
                    'description'      => 'Maîtrisez Figma pour créer des interfaces utilisateur modernes et des expériences utilisateur exceptionnelles. Apprenez les principes fondamentaux du design, la création de prototypes interactifs et la collaboration en équipe.',
                    'what_you_learn'   => "• Maîtriser l'interface Figma\n• Principes fondamentaux du design UI\n• Créer des wireframes et maquettes\n• Prototypes interactifs\n• Design system et composants réutilisables\n• Collaboration et handoff développeurs",
                    'category'         => 'Design UI/UX',
                    'level'            => 'beginner',
                    'language'         => 'fr',
                    'price'            => 10000,
                    'is_free'          => false,
                    'status'           => 'published',
                    'duration_minutes' => 420,
                ],
                'chapters' => [
                    [
                        'title' => 'Prise en main de Figma',
                        'lessons' => [
                            ['title' => 'Présentation de Figma et création de compte', 'type' => 'video', 'duration' => 480, 'is_free' => true, 'video_url' => null],
                            ['title' => 'L\'interface Figma — outils essentiels', 'type' => 'video', 'duration' => 720, 'is_free' => true, 'video_url' => null],
                            ['title' => 'Formes, couleurs et typographie', 'type' => 'video', 'duration' => 900, 'is_free' => false, 'video_url' => null],
                        ],
                    ],
                    [
                        'title' => 'Principes du design UI',
                        'lessons' => [
                            ['title' => 'Théorie des couleurs pour le digital', 'type' => 'video', 'duration' => 780, 'is_free' => false, 'video_url' => null],
                            ['title' => 'Typographie — choisir et combiner les polices', 'type' => 'video', 'duration' => 660, 'is_free' => false, 'video_url' => null],
                            ['title' => 'Grilles et mise en page', 'type' => 'text', 'duration' => 360, 'is_free' => false, 'video_url' => null, 'content' => "## Les grilles en design\n\nUne grille structure votre interface.\n\n**Règle des 8px** : Utilisez des multiples de 8 pour les espacements."],
                        ],
                    ],
                    [
                        'title' => 'Créer votre premier projet',
                        'lessons' => [
                            ['title' => 'Projet — Design d\'une app mobile camerounaise', 'type' => 'video', 'duration' => 1800, 'is_free' => false, 'video_url' => null],
                            ['title' => 'Prototypage et interactions', 'type' => 'video', 'duration' => 960, 'is_free' => false, 'video_url' => null],
                            ['title' => 'Export et handoff aux développeurs', 'type' => 'video', 'duration' => 540, 'is_free' => false, 'video_url' => null],
                        ],
                    ],
                ],
            ],

            [
                'course' => [
                    'user_id'          => $teacher2->id,
                    'title'            => 'Marketing Digital — Stratégies pour l\'Afrique',
                    'description'      => 'Apprenez à développer une présence digitale efficace adaptée au marché africain. Ce cours couvre les réseaux sociaux, la publicité en ligne, le SEO et la création de contenu engageant pour votre audience locale.',
                    'what_you_learn'   => "• Stratégie de contenu sur les réseaux sociaux\n• Publicité Facebook et Instagram Ads\n• SEO — référencement naturel\n• Email marketing\n• Analyse des performances avec Google Analytics\n• Créer une communauté engagée",
                    'category'         => 'Marketing digital',
                    'level'            => 'intermediate',
                    'language'         => 'fr',
                    'price'            => 25000,
                    'is_free'          => false,
                    'status'           => 'published',
                    'duration_minutes' => 300,
                ],
                'chapters' => [
                    [
                        'title' => 'Fondamentaux du Marketing Digital',
                        'lessons' => [
                            ['title' => 'Le paysage digital en Afrique en 2024', 'type' => 'video', 'duration' => 600, 'is_free' => true, 'video_url' => null],
                            ['title' => 'Définir votre audience cible', 'type' => 'video', 'duration' => 720, 'is_free' => true, 'video_url' => null],
                            ['title' => 'Créer votre identité de marque', 'type' => 'video', 'duration' => 840, 'is_free' => false, 'video_url' => null],
                        ],
                    ],
                    [
                        'title' => 'Réseaux Sociaux & Contenu',
                        'lessons' => [
                            ['title' => 'Facebook & Instagram — stratégie organique', 'type' => 'video', 'duration' => 960, 'is_free' => false, 'video_url' => null],
                            ['title' => 'TikTok pour les entreprises africaines', 'type' => 'video', 'duration' => 780, 'is_free' => false, 'video_url' => null],
                            ['title' => 'Créer du contenu viral avec peu de budget', 'type' => 'video', 'duration' => 900, 'is_free' => false, 'video_url' => null],
                        ],
                    ],
                    [
                        'title' => 'Publicité Payante',
                        'lessons' => [
                            ['title' => 'Facebook Ads — créer votre première campagne', 'type' => 'video', 'duration' => 1200, 'is_free' => false, 'video_url' => null],
                            ['title' => 'Optimiser vos publicités pour le marché camerounais', 'type' => 'video', 'duration' => 900, 'is_free' => false, 'video_url' => null],
                        ],
                    ],
                ],
            ],

            [
                'course' => [
                    'user_id'          => $teacher1->id,
                    'title'            => 'Développement Mobile avec Flutter',
                    'description'      => 'Créez des applications mobiles professionnelles pour Android et iOS avec un seul code base. Flutter est le framework de Google qui révolutionne le développement mobile. Ce cours vous amènera de l\'installation jusqu\'à la publication de votre première application.',
                    'what_you_learn'   => "• Maîtriser le langage Dart\n• Créer des interfaces avec les widgets Flutter\n• Gestion d'état avec Provider et Riverpod\n• Connexion à des APIs REST\n• Publier sur Google Play et App Store\n• Projet complet : application de paiement mobile",
                    'category'         => 'Développement mobile',
                    'level'            => 'intermediate',
                    'language'         => 'fr',
                    'price'            => 35000,
                    'is_free'          => false,
                    'status'           => 'published',
                    'duration_minutes' => 600,
                ],
                'chapters' => [
                    [
                        'title' => 'Introduction à Flutter & Dart',
                        'lessons' => [
                            ['title' => 'Pourquoi Flutter ? Avantages et écosystème', 'type' => 'video', 'duration' => 540, 'is_free' => true, 'video_url' => null],
                            ['title' => 'Installation Flutter SDK et Android Studio', 'type' => 'video', 'duration' => 900, 'is_free' => true, 'video_url' => null],
                            ['title' => 'Les bases du langage Dart', 'type' => 'video', 'duration' => 1200, 'is_free' => false, 'video_url' => null],
                        ],
                    ],
                    [
                        'title' => 'Widgets & Interface',
                        'lessons' => [
                            ['title' => 'Widgets stateless vs stateful', 'type' => 'video', 'duration' => 840, 'is_free' => false, 'video_url' => null],
                            ['title' => 'Layouts — Column, Row, Stack', 'type' => 'video', 'duration' => 960, 'is_free' => false, 'video_url' => null],
                            ['title' => 'Navigation entre écrans', 'type' => 'video', 'duration' => 780, 'is_free' => false, 'video_url' => null],
                            ['title' => 'Formulaires et validation', 'type' => 'video', 'duration' => 900, 'is_free' => false, 'video_url' => null],
                        ],
                    ],
                    [
                        'title' => 'Projet — App de paiement Mobile Money',
                        'lessons' => [
                            ['title' => 'Architecture du projet', 'type' => 'video', 'duration' => 720, 'is_free' => false, 'video_url' => null],
                            ['title' => 'Intégration API Orange Money', 'type' => 'video', 'duration' => 1500, 'is_free' => false, 'video_url' => null],
                            ['title' => 'Tests et débogage', 'type' => 'video', 'duration' => 840, 'is_free' => false, 'video_url' => null],
                            ['title' => 'Publication sur Google Play', 'type' => 'video', 'duration' => 660, 'is_free' => false, 'video_url' => null],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($coursesData as $data) {
            $course = Course::create($data['course']);

            foreach ($data['chapters'] as $chapterIndex => $chapterData) {
                $chapter = $course->chapters()->create([
                    'title' => $chapterData['title'],
                    'order' => $chapterIndex,
                ]);

                foreach ($chapterData['lessons'] as $lessonIndex => $lessonData) {
                    $chapter->lessons()->create([
                        'title'      => $lessonData['title'],
                        'type'       => $lessonData['type'],
                        'duration'   => $lessonData['duration'],
                        'is_free'    => $lessonData['is_free'],
                        'video_url'  => $lessonData['video_url'] ?? null,
                        'content'    => $lessonData['content'] ?? null,
                        'video_path' => null,
                        'order'      => $lessonIndex,
                    ]);
                }
            }

            $this->command->info("✅ Cours créé : {$course->title}");
        }

        $this->command->info("\n🎉 Seeder terminé — 5 cours créés avec chapitres et leçons !");
    }
}