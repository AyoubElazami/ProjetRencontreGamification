<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\UserMatchRepository")]
#[ORM\Table(name: "user_match")]
#[ORM\Index(columns: ["user_a_id", "user_b_id"])]
class UserMatch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_a_id", nullable: false)]
    private User $userA;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_b_id", nullable: false)]
    private User $userB;

    #[ORM\Column(type: "datetime_immutable")]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?\DateTimeImmutable $lastInteractionAt = null;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: "userMatch", cascade: ["persist", "remove"])]
    private Collection $messages;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->lastInteractionAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getUserA(): User { return $this->userA; }
    public function setUserA(User $userA): self { $this->userA = $userA; return $this; }

    public function getUserB(): User { return $this->userB; }
    public function setUserB(User $userB): self { $this->userB = $userB; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function getLastInteractionAt(): ?\DateTimeImmutable { return $this->lastInteractionAt; }
    public function setLastInteractionAt(?\DateTimeImmutable $lastInteractionAt): self { 
        $this->lastInteractionAt = $lastInteractionAt; 
        return $this; 
    }

    public function getMessages(): Collection { return $this->messages; }
    public function addMessage(Message $message): self {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setUserMatch($this);
        }
        return $this;
    }
}

