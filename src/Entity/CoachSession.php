<?php

namespace App\Entity;

use App\Repository\CoachSessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoachSessionRepository::class)]
#[ORM\Table(name: 'coach_sessions')]
class CoachSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sessions', targetEntity: Coach::class)]
    #[ORM\JoinColumn(name: 'coach_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Coach $coach = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private int $durationMinutes = 60;

    #[ORM\Column(length: 50)]
    private ?string $price = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(mappedBy: 'session', targetEntity: Booking::class)]
    private Collection $bookings;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDurationMinutes(): int
    {
        return $this->durationMinutes;
    }

    public function setDurationMinutes(int $durationMinutes): static
    {
        $this->durationMinutes = $durationMinutes;
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

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }
}
