<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\Table(name: 'bookings')]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bookings', targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'client_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $client = null;

    #[ORM\ManyToOne(inversedBy: 'bookings', targetEntity: Coach::class)]
    #[ORM\JoinColumn(name: 'coach_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Coach $coach = null;

    #[ORM\ManyToOne(inversedBy: 'bookings', targetEntity: CoachSession::class)]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?CoachSession $session = null;

    #[ORM\Column(length: 255)]
    private ?string $sessionTitle = null;

    #[ORM\Column(length: 50)]
    private ?string $price = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $scheduledAt = null;

    #[ORM\Column(length: 30)]
    private string $status = 'pending'; // 'pending', 'accepted', 'denied', 'cancelled', 'completed'

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $clientNotes = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $coachNotes = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(mappedBy: 'booking', targetEntity: Notification::class, cascade: ['remove'])]
    private Collection $notifications;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): static
    {
        $this->client = $client;
        return $this;
    }

    public function getCoach(): ?Coach
    {
        return $this->coach;
    }

    public function setCoach(?Coach $coach): static
    {
        $this->coach = $coach;
        return $this;
    }

    public function getSession(): ?CoachSession
    {
        return $this->session;
    }

    public function setSession(?CoachSession $session): static
    {
        $this->session = $session;
        return $this;
    }

    public function getSessionTitle(): ?string
    {
        return $this->sessionTitle;
    }

    public function setSessionTitle(string $sessionTitle): static
    {
        $this->sessionTitle = $sessionTitle;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getScheduledAt(): ?\DateTimeImmutable
    {
        return $this->scheduledAt;
    }

    public function setScheduledAt(\DateTimeImmutable $scheduledAt): static
    {
        $this->scheduledAt = $scheduledAt;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getClientNotes(): ?string
    {
        return $this->clientNotes;
    }

    public function setClientNotes(?string $clientNotes): static
    {
        $this->clientNotes = $clientNotes;
        return $this;
    }

    public function getCoachNotes(): ?string
    {
        return $this->coachNotes;
    }

    public function setCoachNotes(?string $coachNotes): static
    {
        $this->coachNotes = $coachNotes;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }
}
