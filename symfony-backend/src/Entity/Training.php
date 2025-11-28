<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TrainingRepository;

#[ORM\Entity(repositoryClass: TrainingRepository::class)]
#[ORM\Table(name: "trainings")]
class Training
{
    // Shared primary key with Event
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: Event::class)]
    // añadimos onDelete: 'CASCADE' para que la FK en BD borre esta fila cuando se elimine el event
    #[ORM\JoinColumn(name: "id", referencedColumnName: "id", onDelete: "CASCADE", nullable: false)]
    private Event $event;

    #[ORM\Column(type: 'string', length: 20)]
    private string $training_type;

    #[ORM\Column(type: 'string', length: 50)]
    private string $focus_area;


    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $coach;

    public function getEvent(): Event { return $this->event; }
    public function setEvent(Event $event): self { $this->event = $event; return $this; }

    public function getTrainingType(): string { return $this->training_type; }
    public function setTrainingType(string $training_type): self { $this->training_type = $training_type; return $this; }

    public function getFocusArea(): string { return $this->focus_area; }
    public function setFocusArea(string $focus_area): self { $this->focus_area = $focus_area; return $this; }

    public function getCoach(): User { return $this->coach; }
    public function setCoach(User $coach): self { $this->coach = $coach; return $this; }
}
