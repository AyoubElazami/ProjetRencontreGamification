<?php
namespace App\Controller\Admin;

use App\Entity\Report;
use App\Entity\User;
use App\Service\ModerationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ModerationService $moderationService
    ) {}

    #[Route('/api/admin/reports', name: 'api_admin_reports', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function getReports(): JsonResponse
    {
        $reports = $this->moderationService->getPendingReports();

        return $this->json([
            'reports' => array_map(fn(Report $report) => $this->serializeReport($report), $reports)
        ]);
    }

    #[Route('/api/admin/reports/{id}', name: 'api_admin_report_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showReport(int $id): JsonResponse
    {
        $report = $this->em->getRepository(Report::class)->find($id);
        if (!$report) {
            throw new NotFoundHttpException('Report not found');
        }

        return $this->json($this->serializeReport($report));
    }

    #[Route('/api/admin/reports/{id}/review', name: 'api_admin_report_review', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function reviewReport(int $id, Request $request): JsonResponse
    {
        $report = $this->em->getRepository(Report::class)->find($id);
        if (!$report) {
            throw new NotFoundHttpException('Report not found');
        }

        $data = json_decode($request->getContent(), true);
        $status = $data['status'] ?? null;

        if (!in_array($status, [Report::STATUS_REVIEWED, Report::STATUS_ACTION_TAKEN, Report::STATUS_DISMISSED])) {
            return $this->json(['error' => 'Invalid status'], 400);
        }

        $this->moderationService->reviewReport($report, $status, $data['notes'] ?? null);

        return $this->json($this->serializeReport($report));
    }

    #[Route('/api/admin/users/{id}/ban', name: 'api_admin_user_ban', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function banUser(int $id, Request $request): JsonResponse
    {
        $user = $this->em->getRepository(User::class)->find($id);
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $data = json_decode($request->getContent(), true);
        $until = isset($data['until']) ? new \DateTimeImmutable($data['until']) : null;

        $this->moderationService->banUser($user, $until);

        return $this->json(['success' => true, 'message' => 'User banned']);
    }

    #[Route('/api/admin/users/{id}/unban', name: 'api_admin_user_unban', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function unbanUser(int $id): JsonResponse
    {
        $user = $this->em->getRepository(User::class)->find($id);
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $this->moderationService->unbanUser($user);

        return $this->json(['success' => true, 'message' => 'User unbanned']);
    }

    private function serializeReport(Report $report): array
    {
        return [
            'id' => $report->getId(),
            'reporter' => [
                'id' => $report->getReporter()->getId(),
                'username' => $report->getReporter()->getUsername()
            ],
            'targetUser' => [
                'id' => $report->getTargetUser()->getId(),
                'username' => $report->getTargetUser()->getUsername()
            ],
            'reason' => $report->getReason(),
            'details' => $report->getDetails(),
            'status' => $report->getStatus(),
            'createdAt' => $report->getCreatedAt()?->format('c'),
            'reviewedAt' => $report->getReviewedAt()?->format('c')
        ];
    }
}

