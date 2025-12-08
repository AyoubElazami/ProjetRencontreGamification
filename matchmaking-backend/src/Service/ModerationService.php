<?php
namespace App\Service;

use App\Entity\Report;
use App\Entity\User;
use App\Repository\ReportRepository;
use Doctrine\ORM\EntityManagerInterface;

class ModerationService
{
    private array $badWords = ['spam', 'fake', 'inappropriate']; // À étendre avec une vraie liste

    public function __construct(
        private EntityManagerInterface $em,
        private ReportRepository $reportRepo
    ) {}

    public function createReport(User $reporter, User $targetUser, string $reason, ?string $details = null): Report
    {
        $report = new Report();
        $report->setReporter($reporter);
        $report->setTargetUser($targetUser);
        $report->setReason($reason);
        $report->setDetails($details);
        $report->setStatus(Report::STATUS_PENDING);

        $this->em->persist($report);
        $this->em->flush();

        return $report;
    }

    public function reviewReport(Report $report, string $status, ?string $notes = null): void
    {
        $report->setStatus($status);
        $report->setReviewedAt(new \DateTimeImmutable());

        if ($status === Report::STATUS_ACTION_TAKEN) {
            // Actions possibles: ban temporaire, ban permanent, avertissement, etc.
            // Implémenter selon les besoins
        }

        $this->em->flush();
    }

    public function banUser(User $user, ?\DateTimeImmutable $until = null): void
    {
        $user->setRoles(['ROLE_BANNED']);
        $this->em->flush();
    }

    public function unbanUser(User $user): void
    {
        $roles = $user->getRoles();
        $roles = array_filter($roles, fn($role) => $role !== 'ROLE_BANNED');
        $user->setRoles(array_values($roles));
        $this->em->flush();
    }

    public function containsBadWords(string $text): bool
    {
        $lowerText = strtolower($text);
        foreach ($this->badWords as $badWord) {
            if (str_contains($lowerText, strtolower($badWord))) {
                return true;
            }
        }
        return false;
    }

    public function sanitizeContent(string $content): string
    {
        // Nettoyer le contenu
        $content = strip_tags($content);
        $content = trim($content);
        
        return $content;
    }

    public function getPendingReports(): array
    {
        return $this->reportRepo->findPendingReports();
    }
}

