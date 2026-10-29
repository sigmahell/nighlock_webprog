<?php

namespace App\Entity;

use App\Repository\CoachRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoachRepository::class)]
#[ORM\Table(name: 'coaches')]
class Coach
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'coachProfile', targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $specialization = null;

    #[ORM\Column(length: 20)]
    private string $tier = 'community'; // 'featured', 'pro', 'community'

    #[ORM\Column(length: 20)]
    private string $status = 'pending'; // 'pending', 'approved', 'rejected'

    #[ORM\Column(length: 10)]
    private string $rating = '10.0';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $hourlyRate = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, CoachSession>
     */
    #[ORM\OneToMany(mappedBy: 'coach', targetEntity: CoachSession::class, cascade: ['persist', 'remove'])]
    private Collection $sessions;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(mappedBy: 'coach', targetEntity: Booking::class, cascade: ['remove'])]
    private Collection $bookings;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->sessions = new ArrayCollection();
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getSpecialization(): ?string
    {
        return $this->specialization;
    }

    public function setSpecialization(string $specialization): static
    {
        $this->specialization = $specialization;
        return $this;
    }

    public function getTier(): string
    {
        return $this->tier;
    }

    public function setTier(string $tier): static
    {
        $this->tier = $tier;
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

    public function getRating(): string
    {
        return $this->rating;
    }

    public function setRating(string $rating): static
    {
        $this->rating = $rating;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;
        return $this;
    }

    public function getHourlyRate(): ?string
    {
        return $this->hourlyRate;
    }

    public function setHourlyRate(?string $hourlyRate): static
    {
        $this->hourlyRate = $hourlyRate;
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
     * @return Collection<int, CoachSession>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(CoachSession $session): static
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setCoach($this);
        }
        return $this;
    }

    public function removeSession(CoachSession $session): static
    {
        if ($this->sessions->removeElement($session)) {
            if ($session->getCoach() === $this) {
                $session->setCoach(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }
}
