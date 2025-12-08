<?php
namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\ReportRepository")]
#[ORM\Table(name: "report")]
#[ORM\Index(columns: ["target_user_id", "status"])]
#[ORM\Index(columns: ["reporter_id"])]
class Report
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_REVIEWED = 'reviewed';
    public const STATUS_ACTION_TAKEN = 'action_taken';
    public const STATUS_DISMISSED = 'dismissed';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "reportsMade")]
    #[ORM\JoinColumn(nullable: false)]
    private User $reporter;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $targetUser;

    #[ORM\Column(type: "string", length: 255)]
    private string $reason;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $details = null;

    #[ORM\Column(type: "string", length: 50)]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column(type: "datetime_immutable")]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?\DateTimeImmutable $reviewedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getReporter(): User { return $this->reporter; }
    public function setReporter(User $reporter): self { $this->reporter = $reporter; return $this; }

    public function getTargetUser(): User { return $this->targetUser; }
    public function setTargetUser(User $targetUser): self { $this->targetUser = $targetUser; return $this; }

    public function getReason(): string { return $this->reason; }
    public function setReason(string $reason): self { $this->reason = $reason; return $this; }

    public function getDetails(): ?string { return $this->details; }
    public function setDetails(?string $details): self { $this->details = $details; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function getReviewedAt(): ?\DateTimeImmutable { return $this->reviewedAt; }
    public function setReviewedAt(?\DateTimeImmutable $reviewedAt): self { $this->reviewedAt = $reviewedAt; return $this; }
}

