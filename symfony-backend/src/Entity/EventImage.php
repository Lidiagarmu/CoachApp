<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\EventImageRepository;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: EventImageRepository::class)]
#[ORM\Table(name: "event_images")]
class EventImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $url;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    private Event $event;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $uploaded_at;

    public function __construct() { $this->uploaded_at = new \DateTime(); }

    public function getId(): int { return $this->id; }

    public function getUrl(): string { return $this->url; }
    public function setUrl(string $url): self { $this->url = $url; return $this; }

    public function getEvent(): Event { return $this->event; }
    public function setEvent(Event $event): self { $this->event = $event; return $this; }

    public function getUploadedAt(): \DateTimeInterface { return $this->uploaded_at; }
    public function setUploadedAt(\DateTimeInterface $uploaded_at): self { $this->uploaded_at = $uploaded_at; return $this; }
}
