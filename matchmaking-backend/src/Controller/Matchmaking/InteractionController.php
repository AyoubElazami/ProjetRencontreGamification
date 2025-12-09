<?php
namespace App\Controller\Matchmaking;

use App\Entity\MatchRequest;
use App\Entity\User;
use App\Service\GamificationService;
use App\Service\MatchService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class InteractionController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private MatchService $matchService,
        private GamificationService $gamificationService
    ) {}

    #[Route('/api/users/{id}/like', name: 'api_user_like', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function like(int $id): JsonResponse
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $targetUser = $this->em->getRepository(User::class)->find($id);
        if (!$targetUser) {
            throw new NotFoundHttpException('User not found');
        }

        if ($currentUser->getId() === $targetUser->getId()) {
            return $this->json(['error' => 'Cannot like yourself'], 400);
        }

        try {
            $matchRequest = $this->matchService->createMatchRequest($currentUser, $targetUser, MatchRequest::TYPE_LIKE);
            
            return $this->json([
                'success' => true,
                'matchRequest' => [
                    'id' => $matchRequest->getId(),
                    'type' => $matchRequest->getType(),
                    'status' => $matchRequest->getStatus()
                ]
            ], 201);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/api/users/{id}/dislike', name: 'api_user_dislike', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function dislike(int $id): JsonResponse
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $targetUser = $this->em->getRepository(User::class)->find($id);
        if (!$targetUser) {
            throw new NotFoundHttpException('User not found');
        }

        try {
            $matchRequest = $this->matchService->createMatchRequest($currentUser, $targetUser, MatchRequest::TYPE_DISLIKE);
            
            return $this->json([
                'success' => true,
                'matchRequest' => [
                    'id' => $matchRequest->getId(),
                    'type' => $matchRequest->getType(),
                    'status' => $matchRequest->getStatus()
                ]
            ], 201);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/api/users/{id}/superlike', name: 'api_user_superlike', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function superlike(int $id): JsonResponse
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $targetUser = $this->em->getRepository(User::class)->find($id);
        if (!$targetUser) {
            throw new NotFoundHttpException('User not found');
        }

        try {
            $matchRequest = $this->matchService->createMatchRequest($currentUser, $targetUser, MatchRequest::TYPE_SUPERLIKE);
            
            // Ajouter XP pour superlike
            $this->gamificationService->addXp($currentUser, $this->gamificationService->getXpForSuperlike(), 'Superlike sent');

            return $this->json([
                'success' => true,
                'matchRequest' => [
                    'id' => $matchRequest->getId(),
                    'type' => $matchRequest->getType(),
                    'status' => $matchRequest->getStatus()
                ]
            ], 201);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/api/users/{id}/wink', name: 'api_user_wink', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function wink(int $id): JsonResponse
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $targetUser = $this->em->getRepository(User::class)->find($id);
        if (!$targetUser) {
            throw new NotFoundHttpException('User not found');
        }

        try {
            $matchRequest = $this->matchService->createMatchRequest($currentUser, $targetUser, MatchRequest::TYPE_WINK);
            
            return $this->json([
                'success' => true,
                'matchRequest' => [
                    'id' => $matchRequest->getId(),
                    'type' => $matchRequest->getType(),
                    'status' => $matchRequest->getStatus()
                ]
            ], 201);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/api/likes/received', name: 'api_likes_received', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getReceivedLikes(): JsonResponse
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // Récupérer toutes les requêtes pending où l'utilisateur actuel est le destinataire
        $receivedRequests = $this->em->getRepository(MatchRequest::class)->findBy([
            'toUser' => $currentUser,
            'status' => MatchRequest::STATUS_PENDING
        ], ['createdAt' => 'DESC']);

        // Filtrer seulement les likes et superlikes (pas les dislikes)
        $likes = array_filter($receivedRequests, function(MatchRequest $request) {
            return in_array($request->getType(), [MatchRequest::TYPE_LIKE, MatchRequest::TYPE_SUPERLIKE]);
        });

        return $this->json([
            'likes' => array_map(function(MatchRequest $request) {
                $fromUser = $request->getFromUser();
                return [
                    'id' => $request->getId(),
                    'type' => $request->getType(),
                    'user' => [
                        'id' => $fromUser->getId(),
                        'username' => $fromUser->getUsername(),
                        'email' => $fromUser->getEmail(),
                        'avatarUrl' => $fromUser->getAvatarUrl(),
                        'bio' => $fromUser->getBio(),
                        'age' => $fromUser->getAge(),
                        'location' => $fromUser->getLocation(),
                        'level' => $fromUser->getLevel(),
                        'xp' => $fromUser->getXp(),
                        'tags' => array_map(fn($tag) => $tag->getName(), $fromUser->getTags()->toArray())
                    ],
                    'createdAt' => $request->getCreatedAt()?->format('c')
                ];
            }, $likes)
        ]);
    }
}

