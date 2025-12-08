<?php

namespace App\Command;

use App\Entity\Badge;
use App\Entity\Quest;
use App\Entity\ProfileTag;
use App\Entity\User;
use App\Entity\UserBadge;
use App\Entity\UserQuest;
use App\Entity\UserMatch;
use App\Entity\MatchRequest;
use App\Entity\Message;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:load-fixtures',
    description: 'Remplit la base de données avec des données de test'
)]
class LoadFixturesCommand extends Command
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
        $io->title('Chargement des fixtures...');

        // Nettoyer les données existantes (optionnel - commenté pour sécurité)
        // $this->clearDatabase($io);

        // Créer les badges
        $io->section('Création des badges...');
        $badges = $this->createBadges();
        $io->success(sprintf('%d badges créés', count($badges)));

        // Créer les quêtes
        $io->section('Création des quêtes...');
        $quests = $this->createQuests();
        $io->success(sprintf('%d quêtes créées', count($quests)));

        // Créer les tags
        $io->section('Création des tags...');
        $tags = $this->createTags();
        $io->success(sprintf('%d tags créés', count($tags)));

        // Créer les utilisateurs
        $io->section('Création des utilisateurs...');
        $users = $this->createUsers($tags);
        $io->success(sprintf('%d utilisateurs créés', count($users)));

        // Créer des badges pour certains utilisateurs
        $io->section('Attribution de badges...');
        $this->assignBadges($users, $badges);
        $io->success('Badges attribués');

        // Créer des quêtes pour certains utilisateurs
        $io->section('Attribution de quêtes...');
        $this->assignQuests($users, $quests);
        $io->success('Quêtes attribuées');

        // Créer des matches
        $io->section('Création de matches...');
        $matches = $this->createMatches($users);
        $io->success(sprintf('%d matches créés', count($matches)));

        // Créer des messages
        $io->section('Création de messages...');
        $messages = $this->createMessages($matches, $users);
        $io->success(sprintf('%d messages créés', count($messages)));

        // Créer des notifications
        $io->section('Création de notifications...');
        $notifications = $this->createNotifications($users);
        $io->success(sprintf('%d notifications créées', count($notifications)));

        // Créer des match requests
        $io->section('Création de match requests...');
        $matchRequests = $this->createMatchRequests($users);
        $io->success(sprintf('%d match requests créées', count($matchRequests)));

        $io->newLine();
        $io->success('✅ Toutes les fixtures ont été chargées avec succès !');

        return Command::SUCCESS;
    }

    private function createBadges(): array
    {
        $badgesData = [
            ['code' => 'level_5', 'name' => 'Niveau 5', 'description' => 'Atteindre le niveau 5', 'icon' => '🎯', 'xpReward' => 50],
            ['code' => 'level_10', 'name' => 'Niveau 10', 'description' => 'Atteindre le niveau 10', 'icon' => '⭐', 'xpReward' => 100],
            ['code' => 'level_25', 'name' => 'Niveau 25', 'description' => 'Atteindre le niveau 25', 'icon' => '🏆', 'xpReward' => 250],
            ['code' => 'first_match', 'name' => 'Premier Match', 'description' => 'Créer votre premier match', 'icon' => '💕', 'xpReward' => 30],
            ['code' => 'social_butterfly', 'name' => 'Papillon Social', 'description' => 'Envoyer 50 messages', 'icon' => '💬', 'xpReward' => 75],
            ['code' => 'superliker', 'name' => 'Super Liker', 'description' => 'Faire 10 superlikes', 'icon' => '✨', 'xpReward' => 50],
            ['code' => 'match_maker', 'name' => 'Cupidon', 'description' => 'Créer 20 matches', 'icon' => '💘', 'xpReward' => 150],
        ];

        $badges = [];
        foreach ($badgesData as $data) {
            $existing = $this->em->getRepository(Badge::class)->findOneBy(['code' => $data['code']]);
            if ($existing) {
                $badges[] = $existing;
                continue;
            }

            $badge = new Badge();
            $badge->setCode($data['code']);
            $badge->setName($data['name']);
            $badge->setDescription($data['description']);
            $badge->setIcon($data['icon']);
            $badge->setXpReward($data['xpReward']);

            $this->em->persist($badge);
            $badges[] = $badge;
        }

        $this->em->flush();
        return $badges;
    }

    private function createQuests(): array
    {
        $questsData = [
            [
                'code' => 'quest_first_match',
                'title' => 'Premier Match',
                'description' => 'Créez votre premier match',
                'xpReward' => 50,
                'conditions' => ['matches' => 1]
            ],
            [
                'code' => 'quest_5_matches',
                'title' => '5 Matches',
                'description' => 'Créez 5 matches',
                'xpReward' => 100,
                'conditions' => ['matches' => 5]
            ],
            [
                'code' => 'quest_10_messages',
                'title' => '10 Messages',
                'description' => 'Envoyez 10 messages',
                'xpReward' => 75,
                'conditions' => ['messages' => 10]
            ],
            [
                'code' => 'quest_3_superlikes',
                'title' => '3 Superlikes',
                'description' => 'Faites 3 superlikes',
                'xpReward' => 60,
                'conditions' => ['superlikes' => 3]
            ],
            [
                'code' => 'quest_complete_profile',
                'title' => 'Profil Complet',
                'description' => 'Complétez votre profil (bio, age, location)',
                'xpReward' => 40,
                'conditions' => ['profile_complete' => 1]
            ],
        ];

        $quests = [];
        foreach ($questsData as $data) {
            $existing = $this->em->getRepository(Quest::class)->findOneBy(['code' => $data['code']]);
            if ($existing) {
                $quests[] = $existing;
                continue;
            }

            $quest = new Quest();
            $quest->setCode($data['code']);
            $quest->setTitle($data['title']);
            $quest->setDescription($data['description']);
            $quest->setXpReward($data['xpReward']);
            $quest->setConditions($data['conditions']);

            $this->em->persist($quest);
            $quests[] = $quest;
        }

        $this->em->flush();
        return $quests;
    }

    private function createTags(): array
    {
        $tagsData = [
            'Sport', 'Musique', 'Cinéma', 'Voyage', 'Cuisine', 'Lecture',
            'Art', 'Gaming', 'Nature', 'Photographie', 'Danse', 'Théâtre',
            'Mode', 'Tech', 'Science', 'Histoire', 'Philosophie', 'Yoga',
            'Fitness', 'Randonnée', 'Plage', 'Montagne', 'Ville', 'Campagne'
        ];

        $tags = [];
        foreach ($tagsData as $tagName) {
            $existing = $this->em->getRepository(ProfileTag::class)->findOneBy(['name' => $tagName]);
            if ($existing) {
                $tags[] = $existing;
                continue;
            }

            $tag = new ProfileTag();
            $tag->setName($tagName);
            $this->em->persist($tag);
            $tags[] = $tag;
        }

        $this->em->flush();
        return $tags;
    }

    private function createUsers(array $tags): array
    {
        $usersData = [
            ['email' => 'alice@example.com', 'username' => 'Alice', 'gender' => 'female', 'age' => 25, 'bio' => 'Passionnée de voyage et de photographie', 'location' => 'Paris, France', 'lat' => '48.8566', 'lon' => '2.3522', 'tags' => ['Voyage', 'Photographie', 'Art'], 'xp' => 150, 'level' => 2],
            ['email' => 'bob@example.com', 'username' => 'Bob', 'gender' => 'male', 'age' => 28, 'bio' => 'Amateur de sport et de musique', 'location' => 'Lyon, France', 'lat' => '45.7640', 'lon' => '4.8357', 'tags' => ['Sport', 'Musique', 'Gaming'], 'xp' => 250, 'level' => 3],
            ['email' => 'charlie@example.com', 'username' => 'Charlie', 'gender' => 'male', 'age' => 30, 'bio' => 'Cinéphile et gourmet', 'location' => 'Marseille, France', 'lat' => '43.2965', 'lon' => '5.3698', 'tags' => ['Cinéma', 'Cuisine', 'Lecture'], 'xp' => 80, 'level' => 1],
            ['email' => 'diana@example.com', 'username' => 'Diana', 'gender' => 'female', 'age' => 27, 'bio' => 'Yoga et bien-être', 'location' => 'Toulouse, France', 'lat' => '43.6047', 'lon' => '1.4442', 'tags' => ['Yoga', 'Nature', 'Fitness'], 'xp' => 320, 'level' => 4],
            ['email' => 'eve@example.com', 'username' => 'Eve', 'gender' => 'female', 'age' => 24, 'bio' => 'Artiste et créative', 'location' => 'Nice, France', 'lat' => '43.7102', 'lon' => '7.2620', 'tags' => ['Art', 'Photographie', 'Mode'], 'xp' => 200, 'level' => 3],
            ['email' => 'frank@example.com', 'username' => 'Frank', 'gender' => 'male', 'age' => 32, 'bio' => 'Tech enthusiast et développeur', 'location' => 'Bordeaux, France', 'lat' => '44.8378', 'lon' => '-0.5792', 'tags' => ['Tech', 'Gaming', 'Science'], 'xp' => 450, 'level' => 5],
            ['email' => 'grace@example.com', 'username' => 'Grace', 'gender' => 'female', 'age' => 26, 'bio' => 'Danseuse et passionnée de théâtre', 'location' => 'Nantes, France', 'lat' => '47.2184', 'lon' => '-1.5536', 'tags' => ['Danse', 'Théâtre', 'Art'], 'xp' => 180, 'level' => 2],
            ['email' => 'henry@example.com', 'username' => 'Henry', 'gender' => 'male', 'age' => 29, 'bio' => 'Randonneur et amoureux de la nature', 'location' => 'Grenoble, France', 'lat' => '45.1885', 'lon' => '5.7245', 'tags' => ['Randonnée', 'Nature', 'Montagne'], 'xp' => 120, 'level' => 2],
            ['email' => 'iris@example.com', 'username' => 'Iris', 'gender' => 'female', 'age' => 23, 'bio' => 'Plage et soleil', 'location' => 'Cannes, France', 'lat' => '43.5528', 'lon' => '7.0174', 'tags' => ['Plage', 'Voyage', 'Mode'], 'xp' => 90, 'level' => 1],
            ['email' => 'jack@example.com', 'username' => 'Jack', 'gender' => 'male', 'age' => 31, 'bio' => 'Historien et philosophe', 'location' => 'Strasbourg, France', 'lat' => '48.5734', 'lon' => '7.7521', 'tags' => ['Histoire', 'Philosophie', 'Lecture'], 'xp' => 280, 'level' => 3],
            ['email' => 'kate@example.com', 'username' => 'Kate', 'gender' => 'female', 'age' => 25, 'bio' => 'Fitness et santé', 'location' => 'Lille, France', 'lat' => '50.6292', 'lon' => '3.0573', 'tags' => ['Fitness', 'Sport', 'Yoga'], 'xp' => 220, 'level' => 3],
            ['email' => 'liam@example.com', 'username' => 'Liam', 'gender' => 'male', 'age' => 27, 'bio' => 'Gamer et tech', 'location' => 'Rennes, France', 'lat' => '48.1173', 'lon' => '-1.6778', 'tags' => ['Gaming', 'Tech', 'Science'], 'xp' => 380, 'level' => 4],
            ['email' => 'mia@example.com', 'username' => 'Mia', 'gender' => 'female', 'age' => 24, 'bio' => 'Ville et culture', 'location' => 'Montpellier, France', 'lat' => '43.6108', 'lon' => '3.8767', 'tags' => ['Ville', 'Art', 'Cinéma'], 'xp' => 160, 'level' => 2],
            ['email' => 'noah@example.com', 'username' => 'Noah', 'gender' => 'male', 'age' => 28, 'bio' => 'Campagne et tranquillité', 'location' => 'Dijon, France', 'lat' => '47.3220', 'lon' => '5.0415', 'tags' => ['Campagne', 'Nature', 'Lecture'], 'xp' => 140, 'level' => 2],
            ['email' => 'olivia@example.com', 'username' => 'Olivia', 'gender' => 'female', 'age' => 26, 'bio' => 'Mode et style', 'location' => 'Reims, France', 'lat' => '49.2583', 'lon' => '4.0317', 'tags' => ['Mode', 'Art', 'Voyage'], 'xp' => 190, 'level' => 2],
        ];

        $users = [];
        foreach ($usersData as $data) {
            $existing = $this->em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
            if ($existing) {
                $users[] = $existing;
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
            $user->setScore($data['xp'] + (rand(0, 10) * 10)); // Score basé sur XP + matches

            // Hash password
            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password123');
            $user->setPassword($hashedPassword);

            // Préférences
            $user->setPreferences([
                'age_range' => [20, 35],
                'distance_km' => 50,
                'gender_pref' => $data['gender'] === 'female' ? 'male' : 'female'
            ]);

            // Tags
            foreach ($data['tags'] as $tagName) {
                $tag = array_filter($tags, fn($t) => $t->getName() === $tagName)[0] ?? null;
                if ($tag) {
                    $user->addTag($tag);
                }
            }

            $this->em->persist($user);
            $users[] = $user;
        }

        $this->em->flush();
        return $users;
    }

    private function assignBadges(array $users, array $badges): void
    {
        $badgeMap = [];
        foreach ($badges as $badge) {
            $badgeMap[$badge->getCode()] = $badge;
        }

        // Attribuer des badges selon les niveaux
        foreach ($users as $user) {
            $level = $user->getLevel();
            
            if ($level >= 5 && isset($badgeMap['level_5'])) {
                $this->assignBadgeToUser($user, $badgeMap['level_5']);
            }
            if ($level >= 10 && isset($badgeMap['level_10'])) {
                $this->assignBadgeToUser($user, $badgeMap['level_10']);
            }
            if ($level >= 25 && isset($badgeMap['level_25'])) {
                $this->assignBadgeToUser($user, $badgeMap['level_25']);
            }
        }

        $this->em->flush();
    }

    private function assignBadgeToUser(User $user, Badge $badge): void
    {
        $existing = $this->em->getRepository(\App\Entity\UserBadge::class)
            ->createQueryBuilder('ub')
            ->where('ub.user = :user')
            ->andWhere('ub.badge = :badge')
            ->setParameter('user', $user)
            ->setParameter('badge', $badge)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$existing) {
            $userBadge = new UserBadge();
            $userBadge->setUser($user);
            $userBadge->setBadge($badge);
            $this->em->persist($userBadge);
        }
    }

    private function assignQuests(array $users, array $quests): void
    {
        foreach ($users as $user) {
            // Assigner quelques quêtes aléatoirement
            $randomQuests = array_slice($quests, 0, rand(2, 4));
            
            foreach ($randomQuests as $quest) {
                $existing = $this->em->getRepository(\App\Entity\UserQuest::class)
                    ->createQueryBuilder('uq')
                    ->where('uq.user = :user')
                    ->andWhere('uq.quest = :quest')
                    ->setParameter('user', $user)
                    ->setParameter('quest', $quest)
                    ->getQuery()
                    ->getOneOrNullResult();

                if (!$existing) {
                    $userQuest = new UserQuest();
                    $userQuest->setUser($user);
                    $userQuest->setQuest($quest);
                    
                    // Progression aléatoire
                    $conditions = $quest->getConditions();
                    $progress = [];
                    foreach ($conditions as $key => $value) {
                        $progress[$key] = rand(0, (int)($value * 0.8)); // 0-80% de progression
                    }
                    $userQuest->setProgress($progress);
                    
                    $this->em->persist($userQuest);
                }
            }
        }

        $this->em->flush();
    }

    private function createMatches(array $users): array
    {
        $matches = [];
        $usedPairs = [];

        // Créer quelques matches
        for ($i = 0; $i < min(8, count($users) / 2); $i++) {
            $userA = $users[array_rand($users)];
            $userB = $users[array_rand($users)];
            
            if ($userA->getId() === $userB->getId()) {
                continue;
            }

            $pairKey = min($userA->getId(), $userB->getId()) . '_' . max($userA->getId(), $userB->getId());
            if (isset($usedPairs[$pairKey])) {
                continue;
            }

            $existing = $this->em->getRepository(UserMatch::class)
                ->createQueryBuilder('m')
                ->where('(m.userA = :userA AND m.userB = :userB) OR (m.userA = :userB AND m.userB = :userA)')
                ->setParameter('userA', $userA)
                ->setParameter('userB', $userB)
                ->getQuery()
                ->getOneOrNullResult();

            if (!$existing) {
                $match = new UserMatch();
                $match->setUserA($userA);
                $match->setUserB($userB);
                $match->setLastInteractionAt(new \DateTimeImmutable());
                
                $this->em->persist($match);
                $matches[] = $match;
                $usedPairs[$pairKey] = true;
            }
        }

        $this->em->flush();
        return $matches;
    }

    private function createMessages(array $matches, array $users): array
    {
        $messages = [];
        $messageTemplates = [
            'Salut ! Comment ça va ?',
            'Super de te rencontrer !',
            'Tu as l\'air sympa 😊',
            'On pourrait se voir un jour ?',
            'J\'adore tes photos !',
            'Tu fais quoi ce weekend ?',
            'Salut ! Ça te dit de discuter ?',
            'Hello ! Comment tu vas ?',
        ];

        foreach ($matches as $match) {
            $numMessages = rand(2, 6);
            $userA = $match->getUserA();
            $userB = $match->getUserB();

            for ($i = 0; $i < $numMessages; $i++) {
                $sender = ($i % 2 === 0) ? $userA : $userB;
                $message = new Message();
                $message->setUserMatch($match);
                $message->setSender($sender);
                $message->setContent($messageTemplates[array_rand($messageTemplates)]);
                
                $this->em->persist($message);
                $messages[] = $message;
            }
        }

        $this->em->flush();
        return $messages;
    }

    private function createNotifications(array $users): array
    {
        $notifications = [];
        $types = [
            Notification::TYPE_MATCH,
            Notification::TYPE_LIKE,
            Notification::TYPE_MESSAGE,
        ];

        foreach ($users as $user) {
            $numNotifications = rand(1, 4);
            
            for ($i = 0; $i < $numNotifications; $i++) {
                $otherUser = $users[array_rand($users)];
                if ($otherUser->getId() === $user->getId()) {
                    continue;
                }

                $type = $types[array_rand($types)];
                $notification = new Notification();
                $notification->setUser($user);
                $notification->setType($type);
                $notification->setPayload([
                    'userId' => $otherUser->getId(),
                    'username' => $otherUser->getUsername(),
                    'avatarUrl' => $otherUser->getAvatarUrl()
                ]);

                // Certaines notifications sont déjà lues
                if (rand(0, 1) === 1) {
                    $notification->setReadAt(new \DateTimeImmutable());
                }

                $this->em->persist($notification);
                $notifications[] = $notification;
            }
        }

        $this->em->flush();
        return $notifications;
    }

    private function createMatchRequests(array $users): array
    {
        $matchRequests = [];
        $types = [
            MatchRequest::TYPE_LIKE,
            MatchRequest::TYPE_SUPERLIKE,
            MatchRequest::TYPE_WINK,
        ];

        for ($i = 0; $i < 15; $i++) {
            $fromUser = $users[array_rand($users)];
            $toUser = $users[array_rand($users)];
            
            if ($fromUser->getId() === $toUser->getId()) {
                continue;
            }

            $existing = $this->em->getRepository(MatchRequest::class)
                ->createQueryBuilder('mr')
                ->where('mr.fromUser = :fromUser')
                ->andWhere('mr.toUser = :toUser')
                ->setParameter('fromUser', $fromUser)
                ->setParameter('toUser', $toUser)
                ->getQuery()
                ->getOneOrNullResult();

            if (!$existing) {
                $matchRequest = new MatchRequest();
                $matchRequest->setFromUser($fromUser);
                $matchRequest->setToUser($toUser);
                $matchRequest->setType($types[array_rand($types)]);
                $matchRequest->setStatus(MatchRequest::STATUS_PENDING);

                $this->em->persist($matchRequest);
                $matchRequests[] = $matchRequest;
            }
        }

        $this->em->flush();
        return $matchRequests;
    }
}

