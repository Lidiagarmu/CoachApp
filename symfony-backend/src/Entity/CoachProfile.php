<?php

namespace App\Entity;

use App\Repository\CoachProfileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoachProfileRepository::class)]
class CoachProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'coachProfile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userAccount = null;

    #[ORM\Column(length: 255, nullable:true)]
    private ?string $teamName = null;

    #[ORM\Column]
    private ?int $yearsExperience = null;

    #[ORM\OneToOne(mappedBy: 'coach', targetEntity: Team::class, cascade: ['persist'])]
    private ?Team $team = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserAccount(): ?User
    {
        return $this->userAccount;
    }

    public function setUserAccount(User $userAccount): static
    {
        $this->userAccount = $userAccount;

        return $this;
    }

    public function getTeamName(): ?string
    {
        return $this->teamName;
    }

    public function setTeamName(?string $teamName): self
    {
        $this->teamName = $teamName;
        return $this;
    }

    public function getYearsExperience(): ?int
    {
        return $this->yearsExperience;
    }

    public function setYearsExperience(int $yearsExperience): static
    {
        $this->yearsExperience = $yearsExperience;

        return $this;
    }

        public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
          // Evita bucles infinitos en Doctrine
        if ($this->team !== $team) {
            $this->team = $team;
            if ($team && $team->getCoach() !== $this) {
                $team->setCoach($this);
            }
        }
        return $this;
    
    }

}
