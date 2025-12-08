<?php
namespace App\Controller\Gamification;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GamificationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $userRepo
    ) {}

    #[Route('/api/me/gamification', name: 'api_me_gamification', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function me(): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->json([
            'level' => $user->getLevel(),
            'xp' => $user->getXp(),
            'score' => $user->getScore(),
            'badges' => array_map(function($userBadge) {
                return [
                    'code' => $userBadge->getBadge()->getCode(),
                    'name' => $userBadge->getBadge()->getName(),
                    'description' => $userBadge->getBadge()->getDescription(),
                    'icon' => $userBadge->getBadge()->getIcon(),
                    'awardedAt' => $userBadge->getAwardedAt()?->format('c')
                ];
            }, $user->getUserBadges()->toArray()),
            'quests' => array_map(function($userQuest) {
                return [
                    'code' => $userQuest->getQuest()->getCode(),
                    'title' => $userQuest->getQuest()->getTitle(),
                    'description' => $userQuest->getQuest()->getDescription(),
                    'xpReward' => $userQuest->getQuest()->getXpReward(),
                    'progress' => $userQuest->getProgress(),
                    'completedAt' => $userQuest->getCompletedAt()?->format('c'),
                    'isCompleted' => $userQuest->isCompleted()
                ];
            }, $user->getUserQuests()->toArray())
        ]);
    }

    #[Route('/api/leaderboard', name: 'api_leaderboard', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function leaderboard(Request $request): JsonResponse
    {
        $limit = min(100, max(1, (int)$request->query->get('limit', 10)));

        $users = $this->userRepo->createQueryBuilder('u')
            ->orderBy('u.score', 'DESC')
            ->addOrderBy('u.level', 'DESC')
            ->addOrderBy('u.xp', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $this->json([
            'leaderboard' => array_map(function($user, $index) {
                return [
                    'rank' => $index + 1,
                    'user' => [
                        'id' => $user->getId(),
                        'username' => $user->getUsername(),
                        'avatarUrl' => $user->getAvatarUrl(),
                        'level' => $user->getLevel(),
                        'xp' => $user->getXp(),
                        'score' => $user->getScore()
                    ]
                ];
            }, $users, array_keys($users))
        ]);
    }
}

