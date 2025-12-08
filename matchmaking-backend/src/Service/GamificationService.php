<?php
namespace App\Service;

use App\Entity\Badge;
use App\Entity\User;
use App\Entity\UserBadge;
use App\Entity\UserQuest;
use App\Entity\Quest;
use App\Repository\BadgeRepository;
use App\Repository\QuestRepository;
use App\Repository\UserBadgeRepository;
use App\Repository\UserQuestRepository;
use Doctrine\ORM\EntityManagerInterface;

class GamificationService
{
    private const XP_PER_LEVEL = 100;
    private const XP_FOR_MATCH = 20;
    private const XP_FOR_MESSAGE = 5;
    private const XP_FOR_SUPERLIKE = 10;

    public function __construct(
        private EntityManagerInterface $em,
        private BadgeRepository $badgeRepo,
        private QuestRepository $questRepo,
        private UserBadgeRepository $userBadgeRepo,
        private UserQuestRepository $userQuestRepo
    ) {}

    public function addXp(User $user, int $amount, string $reason = ''): void
    {
        $oldLevel = $user->getLevel();
        $user->setXp($user->getXp() + $amount);

        // Calculer le nouveau niveau
        $newLevel = $this->calculateLevel($user->getXp());
        if ($newLevel > $oldLevel) {
            $user->setLevel($newLevel);
            $this->checkLevelUpBadges($user, $newLevel);
        }

        $this->em->flush();
    }

    public function calculateLevel(int $xp): int
    {
        return (int) floor($xp / self::XP_PER_LEVEL) + 1;
    }

    public function awardBadge(User $user, string $badgeCode): ?UserBadge
    {
        // Vérifier si l'utilisateur a déjà ce badge
        $existing = $this->userBadgeRepo->createQueryBuilder('ub')
            ->join('ub.badge', 'b')
            ->where('ub.user = :user')
            ->andWhere('b.code = :code')
            ->setParameter('user', $user)
            ->setParameter('code', $badgeCode)
            ->getQuery()
            ->getOneOrNullResult();

        if ($existing) {
            return null;
        }

        $badge = $this->badgeRepo->findByCode($badgeCode);
        if (!$badge) {
            return null;
        }

        $userBadge = new UserBadge();
        $userBadge->setUser($user);
        $userBadge->setBadge($badge);

        $this->em->persist($userBadge);

        // Ajouter XP de récompense
        if ($badge->getXpReward() > 0) {
            $this->addXp($user, $badge->getXpReward(), "Badge: {$badge->getName()}");
        }

        $this->em->flush();

        return $userBadge;
    }

    public function updateQuestProgress(User $user, string $questCode, array $progressData): void
    {
        $quest = $this->questRepo->findByCode($questCode);
        if (!$quest) {
            return;
        }

        $userQuest = $this->userQuestRepo->createQueryBuilder('uq')
            ->where('uq.user = :user')
            ->andWhere('uq.quest = :quest')
            ->setParameter('user', $user)
            ->setParameter('quest', $quest)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$userQuest) {
            $userQuest = new UserQuest();
            $userQuest->setUser($user);
            $userQuest->setQuest($quest);
        }

        if ($userQuest->isCompleted()) {
            return; // Déjà complétée
        }

        $userQuest->setProgress($progressData);

        // Vérifier si la quête est complétée
        if ($this->checkQuestCompletion($quest, $progressData)) {
            $userQuest->setCompletedAt(new \DateTimeImmutable());
            $this->addXp($user, $quest->getXpReward(), "Quest: {$quest->getTitle()}");
        }

        $this->em->persist($userQuest);
        $this->em->flush();
    }

    private function checkQuestCompletion(Quest $quest, array $progress): bool
    {
        $conditions = $quest->getConditions();
        if (!$conditions) {
            return false;
        }

        foreach ($conditions as $key => $requiredValue) {
            if (!isset($progress[$key]) || $progress[$key] < $requiredValue) {
                return false;
            }
        }

        return true;
    }

    private function checkLevelUpBadges(User $user, int $level): void
    {
        if ($level === 5) {
            $this->awardBadge($user, 'level_5');
        } elseif ($level === 10) {
            $this->awardBadge($user, 'level_10');
        } elseif ($level === 25) {
            $this->awardBadge($user, 'level_25');
        }
    }

    public function updateLeaderboardScore(User $user): void
    {
        // Score basé sur XP, nombre de matches, messages, etc.
        $score = $user->getXp();
        
        // Ajouter des points pour les matches
        $matches = $user->getMatches();
        $score += count($matches) * 10;

        $user->setScore($score);
        $this->em->flush();
    }

    public function getXpForMatch(): int
    {
        return self::XP_FOR_MATCH;
    }

    public function getXpForMessage(): int
    {
        return self::XP_FOR_MESSAGE;
    }

    public function getXpForSuperlike(): int
    {
        return self::XP_FOR_SUPERLIKE;
    }
}

