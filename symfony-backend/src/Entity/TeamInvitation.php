<?php

namespace App\Entity;

use App\Repository\TeamInvitationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamInvitationRepository::class)]
class TeamInvitation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // El jugador invitado
    #[ORM\ManyToOne(targetEntity: PlayerProfile::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlayerProfile $player = null;

    // El equipo que invita
    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Team $team = null;

    // El entrenador que hace la invitación
    #[ORM\ManyToOne(targetEntity: CoachProfile::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?CoachProfile $coach = null;

    #[ORM\Column(length: 20)]
    private string $status = 'pending'; // pending | accepted | rejected

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // -------------------------------
    // Getters & Setters
    // -------------------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?PlayerProfile
    {
        return $this->player;
    }

    public function setPlayer(PlayerProfile $player): self
    {
        $this->player = $player;
        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(Team $team): self
    {
        $this->team = $team;
        return $this;
    }

    public function getCoach(): ?CoachProfile
    {
        return $this->coach;
    }

    public function setCoach(CoachProfile $coach): self
    {
        $this->coach = $coach;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
}
