<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    private ?string $fullName = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $nickname = null;

    #[ORM\Column(nullable: true)]
    private ?int $age = null;

    #[ORM\OneToOne(mappedBy: 'userAccount', targetEntity: CoachProfile::class, cascade: ['persist', 'remove'])]
    private ?CoachProfile $coachProfile = null;

    #[ORM\OneToOne(mappedBy: 'playerAccount', targetEntity: PlayerProfile::class, cascade: ['persist', 'remove'])]
    private ?PlayerProfile $playerProfile = null;

    // ============================================
    // Getters & Setters base
    // ============================================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): static
    {
        $this->nickname = $nickname;
        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): static
    {
        $this->age = $age;
        return $this;
    }

    // ============================================
    // Perfiles Coach y Player
    // ============================================

    public function getCoachProfile(): ?CoachProfile
    {
        return $this->coachProfile;
    }

    public function setCoachProfile(?CoachProfile $coachProfile): static
    {
        $this->coachProfile = $coachProfile;

        if ($coachProfile && $coachProfile->getUserAccount() !== $this) {
            $coachProfile->setUserAccount($this);
        }

        return $this;
    }

    public function getPlayerProfile(): ?PlayerProfile
    {
        return $this->playerProfile;
    }

    public function setPlayerProfile(?PlayerProfile $playerProfile): static
    {
        $this->playerProfile = $playerProfile;

        if ($playerProfile && $playerProfile->getPlayerAccount() !== $this) {
            $playerProfile->setPlayerAccount($this);
        }

        return $this;
    }

    // ============================================
    // Roles y seguridad
    // ============================================

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }
}
