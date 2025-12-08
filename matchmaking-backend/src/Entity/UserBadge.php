<?php
namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\UserBadgeRepository")]
#[ORM\Table(name: "user_badge")]
#[ORM\Index(columns: ["user_id"])]
class UserBadge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "userBadges")]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Badge::class, inversedBy: "userBadges")]
    #[ORM\JoinColumn(nullable: false)]
    private Badge $badge;

    #[ORM\Column(type: "datetime_immutable")]
    private ?\DateTimeImmutable $awardedAt = null;

    public function __construct()
    {
        $this->awardedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getUser(): User { return $this->user; }
    public function setUser(User $user): self { $this->user = $user; return $this; }

    public function getBadge(): Badge { return $this->badge; }
    public function setBadge(Badge $badge): self { $this->badge = $badge; return $this; }

    public function getAwardedAt(): ?\DateTimeImmutable { return $this->awardedAt; }
    public function setAwardedAt(\DateTimeImmutable $awardedAt): self { $this->awardedAt = $awardedAt; return $this; }
}

