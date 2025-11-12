<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\GameRepository;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ORM\Table(name: "games")]
class Game
{
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: Event::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: "id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private Event $event;

    #[ORM\Column(length: 255)]
    private string $opponent;

    #[ORM\Column(type: 'string', length: 20)]
    private string $match_type;


    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Team $team; 
    // Aquí estará el escudo y nombre del equipo (desde la entidad Team)

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): self
    {
        $this->event = $event;
        return $this;
    }

    public function getOpponent(): string
    {
        return $this->opponent;
    }

    public function setOpponent(string $opponent): self
    {
        $this->opponent = $opponent;
        return $this;
    }

    public function getMatchType(): string
    {
        return $this->match_type;
    }

    public function setMatchType(string $match_type): self
    {
        $this->match_type = $match_type;
        return $this;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function setTeam(Team $team): self
    {
        $this->team = $team;
        return $this;
    }
}
