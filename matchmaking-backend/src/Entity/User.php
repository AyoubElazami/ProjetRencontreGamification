<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: "App\Repository\UserRepository")]
#[ORM\Table(name: "`user`")]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:180, unique:true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    #[ORM\Column(type:"json")]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type:"string")]
    private string $password;

    #[ORM\Column(type:"string", length:50, nullable:true)]
    private ?string $username = null;

    #[ORM\Column(type:"text", nullable:true)]
    private ?string $bio = null;

    #[ORM\Column(type:"string", length:20, nullable:true)]
    private ?string $gender = null;

    #[ORM\Column(type:"integer", nullable:true)]
    private ?int $age = null;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    private ?string $location = null;

    #[ORM\Column(type:"decimal", precision:10, scale:7, nullable:true)]
    private ?string $latitude = null;

    #[ORM\Column(type:"decimal", precision:10, scale:7, nullable:true)]
    private ?string $longitude = null;

    #[ORM\Column(type:"json", nullable:true)]
    private ?array $preferences = null;

    #[ORM\Column(type:"integer", options:["default" => 1])]
    private int $level = 1;

    #[ORM\Column(type:"integer", options:["default" => 0])]
    private int $xp = 0;

    #[ORM\Column(type:"integer", options:["default" => 0])]
    private int $score = 0;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    private ?string $avatarUrl = null;

    #[ORM\Column(type:"datetime_immutable")]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type:"datetime_immutable", nullable:true)]
    private ?\DateTimeImmutable $updatedAt = null;

    // Relations
    #[ORM\ManyToMany(targetEntity: ProfileTag::class, inversedBy: "users")]
    #[ORM\JoinTable(name: "user_profile_tag")]
    private Collection $tags;

    #[ORM\OneToMany(targetEntity: MatchRequest::class, mappedBy: "fromUser", cascade: ["persist"])]
    private Collection $sentMatchRequests;

    #[ORM\OneToMany(targetEntity: MatchRequest::class, mappedBy: "toUser", cascade: ["persist"])]
    private Collection $receivedMatchRequests;

    #[ORM\OneToMany(targetEntity: UserMatch::class, mappedBy: "userA", cascade: ["persist"])]
    private Collection $matchesAsA;

    #[ORM\OneToMany(targetEntity: UserMatch::class, mappedBy: "userB", cascade: ["persist"])]
    private Collection $matchesAsB;

    #[ORM\OneToMany(targetEntity: UserBadge::class, mappedBy: "user", cascade: ["persist"])]
    private Collection $userBadges;

    #[ORM\OneToMany(targetEntity: UserQuest::class, mappedBy: "user", cascade: ["persist"])]
    private Collection $userQuests;

    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: "user", cascade: ["persist"])]
    private Collection $notifications;

    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: "reporter", cascade: ["persist"])]
    private Collection $reportsMade;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->sentMatchRequests = new ArrayCollection();
        $this->receivedMatchRequests = new ArrayCollection();
        $this->matchesAsA = new ArrayCollection();
        $this->matchesAsB = new ArrayCollection();
        $this->userBadges = new ArrayCollection();
        $this->userQuests = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->reportsMade = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    // ---------- Getters / Setters ----------
    public function getId(): ?int { return $this->id; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getUserIdentifier(): string { return (string) $this->email; }

    public function getRoles(): array {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }
    public function setRoles(array $roles): self { $this->roles = $roles; return $this; }

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }

    public function eraseCredentials(): void {}

    public function getUsername(): ?string { return $this->username; }
    public function setUsername(?string $username): self { $this->username = $username; return $this; }

    public function getBio(): ?string { return $this->bio; }
    public function setBio(?string $bio): self { $this->bio = $bio; return $this; }

    public function getGender(): ?string { return $this->gender; }
    public function setGender(?string $gender): self { $this->gender = $gender; return $this; }

    public function getAge(): ?int { return $this->age; }
    public function setAge(?int $age): self { $this->age = $age; return $this; }

    public function getLocation(): ?string { return $this->location; }
    public function setLocation(?string $location): self { $this->location = $location; return $this; }

    public function getLatitude(): ?string { return $this->latitude; }
    public function setLatitude(?string $latitude): self { $this->latitude = $latitude; return $this; }

    public function getLongitude(): ?string { return $this->longitude; }
    public function setLongitude(?string $longitude): self { $this->longitude = $longitude; return $this; }

    public function getPreferences(): ?array { return $this->preferences; }
    public function setPreferences(?array $preferences): self { $this->preferences = $preferences; return $this; }

    public function getLevel(): int { return $this->level; }
    public function setLevel(int $level): self { $this->level = $level; return $this; }

    public function getXp(): int { return $this->xp; }
    public function setXp(int $xp): self { $this->xp = $xp; return $this; }

    public function getScore(): int { return $this->score; }
    public function setScore(int $score): self { $this->score = $score; return $this; }

    public function getAvatarUrl(): ?string { return $this->avatarUrl; }
    public function setAvatarUrl(?string $avatarUrl): self { $this->avatarUrl = $avatarUrl; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }

    // Relations
    public function getTags(): Collection { return $this->tags; }
    public function addTag(ProfileTag $tag): self {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addUser($this);
        }
        return $this;
    }
    public function removeTag(ProfileTag $tag): self {
        $this->tags->removeElement($tag);
        $tag->removeUser($this);
        return $this;
    }

    public function getSentMatchRequests(): Collection { return $this->sentMatchRequests; }
    public function getReceivedMatchRequests(): Collection { return $this->receivedMatchRequests; }
    public function getMatchesAsA(): Collection { return $this->matchesAsA; }
    public function getMatchesAsB(): Collection { return $this->matchesAsB; }
    
    public function getMatches(): Collection
    {
        $matches = new ArrayCollection();
        foreach ($this->matchesAsA as $match) {
            $matches->add($match);
        }
        foreach ($this->matchesAsB as $match) {
            $matches->add($match);
        }
        return $matches;
    }
    public function getUserBadges(): Collection { return $this->userBadges; }
    public function getUserQuests(): Collection { return $this->userQuests; }
    public function getNotifications(): Collection { return $this->notifications; }
    public function getReportsMade(): Collection { return $this->reportsMade; }
}
