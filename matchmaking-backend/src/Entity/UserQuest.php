<?php
namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\UserQuestRepository")]
#[ORM\Table(name: "user_quest")]
#[ORM\Index(columns: ["user_id"])]
class UserQuest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "userQuests")]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Quest::class, inversedBy: "userQuests")]
    #[ORM\JoinColumn(nullable: false)]
    private Quest $quest;

    #[ORM\Column(type: "json", nullable: true)]
    private ?array $progress = null;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

    public function getId(): ?int { return $this->id; }

    public function getUser(): User { return $this->user; }
    public function setUser(User $user): self { $this->user = $user; return $this; }

    public function getQuest(): Quest { return $this->quest; }
    public function setQuest(Quest $quest): self { $this->quest = $quest; return $this; }

    public function getProgress(): ?array { return $this->progress; }
    public function setProgress(?array $progress): self { $this->progress = $progress; return $this; }

    public function getCompletedAt(): ?\DateTimeImmutable { return $this->completedAt; }
    public function setCompletedAt(?\DateTimeImmutable $completedAt): self { $this->completedAt = $completedAt; return $this; }

    public function isCompleted(): bool
    {
        return $this->completedAt !== null;
    }
}

