<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\QuestRepository")]
#[ORM\Table(name: "quest")]
class Quest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 50, unique: true)]
    private string $code;

    #[ORM\Column(type: "string", length: 255)]
    private string $title;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "integer", options: ["default" => 0])]
    private int $xpReward = 0;

    #[ORM\Column(type: "json", nullable: true)]
    private ?array $conditions = null;

    #[ORM\OneToMany(targetEntity: UserQuest::class, mappedBy: "quest")]
    private Collection $userQuests;

    public function __construct()
    {
        $this->userQuests = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getCode(): string { return $this->code; }
    public function setCode(string $code): self { $this->code = $code; return $this; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getXpReward(): int { return $this->xpReward; }
    public function setXpReward(int $xpReward): self { $this->xpReward = $xpReward; return $this; }

    public function getConditions(): ?array { return $this->conditions; }
    public function setConditions(?array $conditions): self { $this->conditions = $conditions; return $this; }

    public function getUserQuests(): Collection { return $this->userQuests; }
}

