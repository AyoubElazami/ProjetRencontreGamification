<?php
namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\MessageRepository")]
#[ORM\Table(name: "message")]
#[ORM\Index(columns: ["match_id", "created_at"])]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: UserMatch::class, inversedBy: "messages")]
    #[ORM\JoinColumn(name: "match_id", nullable: false)]
    private UserMatch $userMatch;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $sender;

    #[ORM\Column(type: "text")]
    private string $content;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?\DateTimeImmutable $readAt = null;

    #[ORM\Column(type: "datetime_immutable")]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getUserMatch(): UserMatch { return $this->userMatch; }
    public function setUserMatch(UserMatch $userMatch): self { $this->userMatch = $userMatch; return $this; }

    public function getSender(): User { return $this->sender; }
    public function setSender(User $sender): self { $this->sender = $sender; return $this; }

    public function getContent(): string { return $this->content; }
    public function setContent(string $content): self { $this->content = $content; return $this; }

    public function getReadAt(): ?\DateTimeImmutable { return $this->readAt; }
    public function setReadAt(?\DateTimeImmutable $readAt): self { $this->readAt = $readAt; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): self { $this->createdAt = $createdAt; return $this; }
}

