<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    private ?string $steamName = null;

    #[ORM\Column(length: 17)]
    private ?string $steamId = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: Coach::class, cascade: ['persist', 'remove'])]
    private ?Coach $coachProfile = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Booking::class, cascade: ['remove'])]
    private Collection $bookings;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(mappedBy: 'recipient', targetEntity: Notification::class, cascade: ['remove'])]
    private Collection $notifications;

    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
        $this->createdAt = new \DateTimeImmutable();
        $this->bookings = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getSteamName(): ?string
    {
        return $this->steamName;
    }

    public function setSteamName(string $steamName): static
    {
        $this->steamName = $steamName;
        return $this;
    }

    public function getSteamId(): ?string
    {
        return $this->steamId;
    }

    public function setSteamId(string $steamId): static
    {
        $this->steamId = $steamId;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Clear sensitive temporary data if any
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

    public function getCoachProfile(): ?Coach
    {
        return $this->coachProfile;
    }

    public function setCoachProfile(?Coach $coachProfile): static
    {
        if ($coachProfile === null && $this->coachProfile !== null) {
            $this->coachProfile->setUser(null);
        }
        if ($coachProfile !== null && $coachProfile->getUser() !== $this) {
            $coachProfile->setUser($this);
        }
        $this->coachProfile = $coachProfile;
        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }
}
