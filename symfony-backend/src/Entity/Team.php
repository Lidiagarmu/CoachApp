<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\OneToOne(inversedBy: 'team', targetEntity: CoachProfile::class)]
    private ?CoachProfile $coach = null;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: PlayerProfile::class)]
    private Collection $players;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

     #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    // ======================
    // Getters / Setters
    // ======================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getCoach(): ?CoachProfile
    {
        return $this->coach;
    }

    public function setCoach(?CoachProfile $coach): self
    {
        $this->coach = $coach;
        return $this;
    }

    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(PlayerProfile $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players[] = $player;
            $player->setTeam($this);
        }

        return $this;
    }

    public function removePlayer(PlayerProfile $player): self
    {
        if ($this->players->removeElement($player)) {
            if ($player->getTeam() === $this) {
                $player->setTeam(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
}
