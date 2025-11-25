<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: "App\Repository\UserRepository")]
#[ORM\Table(name: "`user`")]
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

    #[ORM\Column(type:"string", length:255, nullable:true)]
    private ?string $bio = null;

    #[ORM\Column(type:"integer", options:["default" => 1])]
    private int $level = 1;

    #[ORM\Column(type:"integer", options:["default" => 0])]
    private int $xp = 0;

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

    public function getSalt(): ?string { return null; }
    public function eraseCredentials() { /* si tu stockes des champs sensibles temporaires */ }

    public function getUsername(): ?string { return $this->username; }
    public function setUsername(?string $username): self { $this->username = $username; return $this; }

    public function getBio(): ?string { return $this->bio; }
    public function setBio(?string $bio): self { $this->bio = $bio; return $this; }

    public function getLevel(): int { return $this->level; }
    public function setLevel(int $level): self { $this->level = $level; return $this; }

    public function getXp(): int { return $this->xp; }
    public function setXp(int $xp): self { $this->xp = $xp; return $this; }
}
