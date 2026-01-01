<?php

namespace App\Command;

use App\Entity\ProfileTag;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:add-women-users',
    description: 'Ajoute 3 utilisateurs femmes dans la base de données'
)]
class AddWomenUsersCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Ajout de 3 utilisateurs femmes...');

        // Récupérer les tags existants
        $tags = $this->em->getRepository(ProfileTag::class)->findAll();
        $tagMap = [];
        foreach ($tags as $tag) {
            $tagMap[$tag->getName()] = $tag;
        }

        // Données des 3 nouvelles utilisatrices
        $usersData = [
            [
                'email' => 'sophie@example.com',
                'username' => 'Sophie',
                'gender' => 'female',
                'age' => 28,
                'bio' => 'Passionnée de lecture et de nature, j\'aime les promenades en forêt et découvrir de nouveaux livres',
                'location' => 'Lyon, France',
                'lat' => '45.7640',
                'lon' => '4.8357',
                'tags' => ['Lecture', 'Nature', 'Randonnée', 'Voyage'],
                'xp' => 175,
                'level' => 2
            ],
            [
                'email' => 'laura@example.com',
                'username' => 'Laura',
                'gender' => 'female',
                'age' => 29,
                'bio' => 'Amoureuse de cuisine et de gastronomie, j\'adore cuisiner et partager mes recettes',
                'location' => 'Bordeaux, France',
                'lat' => '44.8378',
                'lon' => '-0.5792',
                'tags' => ['Cuisine', 'Voyage', 'Art', 'Mode'],
                'xp' => 210,
                'level' => 3
            ],
            [
                'email' => 'emma@example.com',
                'username' => 'Emma',
                'gender' => 'female',
                'age' => 26,
                'bio' => 'Fitness et bien-être, j\'aime le sport et mener une vie équilibrée',
                'location' => 'Nice, France',
                'lat' => '43.7102',
                'lon' => '7.2620',
                'tags' => ['Fitness', 'Sport', 'Yoga', 'Nature'],
                'xp' => 195,
                'level' => 2
            ],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($usersData as $data) {
            $existing = $this->em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
            if ($existing) {
                $io->warning(sprintf('L\'utilisateur %s existe déjà', $data['email']));
                $skipped++;
                continue;
            }

            $user = new User();
            $user->setEmail($data['email']);
            $user->setUsername($data['username']);
            $user->setGender($data['gender']);
            $user->setAge($data['age']);
            $user->setBio($data['bio']);
            $user->setLocation($data['location']);
            $user->setLatitude($data['lat']);
            $user->setLongitude($data['lon']);
            $user->setXp($data['xp']);
            $user->setLevel($data['level']);
            $user->setScore($data['xp'] + (rand(0, 10) * 10));

            // Hash password
            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password123');
            $user->setPassword($hashedPassword);

            // Préférences
            $user->setPreferences([
                'age_range' => [25, 35],
                'distance_km' => 50,
                'gender_pref' => 'male'
            ]);

            // Tags
            foreach ($data['tags'] as $tagName) {
                if (isset($tagMap[$tagName])) {
                    $user->addTag($tagMap[$tagName]);
                }
            }

            $this->em->persist($user);
            $created++;
            $io->success(sprintf('Utilisateur %s créé avec succès', $data['username']));
        }

        $this->em->flush();

        $io->newLine();
        $io->success(sprintf('✅ %d utilisateur(s) créé(s), %d ignoré(s)', $created, $skipped));

        return Command::SUCCESS;
    }
}

