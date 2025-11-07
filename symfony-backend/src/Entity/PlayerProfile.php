<?php

namespace App\Entity;

use App\Repository\PlayerProfileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerProfileRepository::class)]
class PlayerProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'playerProfile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $playerAccount = null;

    #[ORM\Column(length: 100)]
    private ?string $position = null;

    #[ORM\Column]
    private ?int $number = null;

    #[ORM\ManyToOne(targetEntity: CoachProfile::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?CoachProfile $coach = null;

    #[ORM\ManyToOne(inversedBy: 'players', targetEntity: Team::class)]
    private ?Team $team = null;

    public function getCoach(): ?CoachProfile
    {
        return $this->coach;
    }

    public function setCoach(?CoachProfile $coach): static
    {
        $this->coach = $coach;
        return $this;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayerAccount(): ?User
    {
        return $this->playerAccount;
    }

    public function setPlayerAccount(User $playerAccount): static
    {
        $this->playerAccount = $playerAccount;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;
        return $this;
    }

}
