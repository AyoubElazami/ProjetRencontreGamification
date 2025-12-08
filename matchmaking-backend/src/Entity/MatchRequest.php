<?php
namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\MatchRequestRepository")]
#[ORM\Table(name: "match_request")]
#[ORM\Index(columns: ["from_user_id", "to_user_id"])]
#[ORM\Index(columns: ["status"])]
class MatchRequest
{
    public const TYPE_LIKE = 'like';
    public const TYPE_DISLIKE = 'dislike';
    public const TYPE_SUPERLIKE = 'superlike';
    public const TYPE_WINK = 'wink';

    public const STATUS_PENDING = 'pending';
    public const STATUS_MATCHED = 'matched';
    public const STATUS_UNMATCHED = 'unmatched';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "sentMatchRequests")]
    #[ORM\JoinColumn(nullable: false)]
    private User $fromUser;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "receivedMatchRequests")]
    #[ORM\JoinColumn(nullable: false)]
    private User $toUser;

    #[ORM\Column(type: "string", length: 20)]
    private string $type = self::TYPE_LIKE;

    #[ORM\Column(type: "string", length: 20)]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column(type: "datetime_immutable")]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getFromUser(): User { return $this->fromUser; }
    public function setFromUser(User $fromUser): self { $this->fromUser = $fromUser; return $this; }

    public function getToUser(): User { return $this->toUser; }
    public function setToUser(User $toUser): self { $this->toUser = $toUser; return $this; }

    public function getType(): string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): self { $this->createdAt = $createdAt; return $this; }
}

