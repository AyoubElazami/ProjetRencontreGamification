<?php
namespace App\Controller\Moderation;

use App\Entity\User;
use App\Service\ModerationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ReportController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ModerationService $moderationService
    ) {}

    #[Route('/api/reports', name: 'api_report_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request): JsonResponse
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $data = json_decode($request->getContent(), true);
        $targetUserId = $data['targetUserId'] ?? null;
        $reason = $data['reason'] ?? null;
        $details = $data['details'] ?? null;

        if (!$targetUserId || !$reason) {
            return $this->json(['error' => 'targetUserId and reason are required'], 400);
        }

        $targetUser = $this->em->getRepository(User::class)->find($targetUserId);
        if (!$targetUser) {
            throw new NotFoundHttpException('Target user not found');
        }

        if ($currentUser->getId() === $targetUser->getId()) {
            return $this->json(['error' => 'Cannot report yourself'], 400);
        }

        $report = $this->moderationService->createReport($currentUser, $targetUser, $reason, $details);

        return $this->json([
            'success' => true,
            'report' => [
                'id' => $report->getId(),
                'status' => $report->getStatus()
            ]
        ], 201);
    }
}

