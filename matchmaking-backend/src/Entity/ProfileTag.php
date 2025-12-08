<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\ProfileTagRepository")]
#[ORM\Table(name: "profile_tag")]
class ProfileTag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 100, unique: true)]
    private string $name;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: "tags")]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getUsers(): Collection { return $this->users; }
    public function addUser(User $user): self {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }
        return $this;
    }
    public function removeUser(User $user): self {
        $this->users->removeElement($user);
        return $this;
    }
}

