<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\Table(name: "events")]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private \DateTimeInterface $date;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private \DateTimeInterface $time;

    #[ORM\Column(type: Types::INTEGER)]
    private int $duration;

    #[ORM\Column(length: 255)]
    private string $location_name;

    #[ORM\Column(length: 255)]
    private string $location_url;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $type = null;


    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $created_by;

    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Team $team = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $created_at;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventImage::class, cascade: ['persist', 'remove'])]
    private Collection $images;

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->images = new ArrayCollection();
    }

    // Getters & Setters
    public function getId() { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }

    public function getDate(): \DateTimeInterface { return $this->date; }
    public function setDate(\DateTimeInterface $date): self { $this->date = $date; return $this; }

    public function getTime(): \DateTimeInterface { return $this->time; }
    public function setTime(\DateTimeInterface $time): self { $this->time = $time; return $this; }

    public function getDuration(): int { return $this->duration; }
    public function setDuration(int $duration): self { $this->duration = $duration; return $this; }

    public function getLocationName(): string { return $this->location_name; }
    public function setLocationName(string $location_name): self { $this->location_name = $location_name; return $this; }

    public function getLocationUrl(): string { return $this->location_url; }
    public function setLocationUrl(string $location_url): self { $this->location_url = $location_url; return $this; }

    public function getType(): string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }

    public function getCreatedBy(): User { return $this->created_by; }
    public function setCreatedBy(User $created_by): self { $this->created_by = $created_by; return $this; }

    public function getTeam(): ?Team { return $this->team; }
    public function setTeam(?Team $team): self { $this->team = $team; return $this; }

    public function getCreatedAt(): \DateTimeInterface { return $this->created_at; }
    public function setCreatedAt(\DateTimeInterface $created_at): self { $this->created_at = $created_at; return $this; }

    /** @return Collection<int, EventImage> */
    public function getImages(): Collection { return $this->images; }

    public function addImage(EventImage $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setEvent($this);
        }
        return $this;
    }

    public function removeImage(EventImage $image): self
    {
        if ($this->images->removeElement($image)) {

        }
        return $this;
    }
}
