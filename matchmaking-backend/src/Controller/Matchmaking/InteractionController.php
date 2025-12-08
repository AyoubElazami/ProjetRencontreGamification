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
}

