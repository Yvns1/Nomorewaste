<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\CookingClassRepository;

#[ORM\Entity(repositoryClass: CookingClassRepository::class)]
class CookingClass
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('cooking_class:read')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups('cooking_class:read')]
    private $title;

    #[ORM\Column(type: 'text')]
    #[Groups('cooking_class:read')]
    private $description;

    #[ORM\Column(type: 'datetime')]
    #[Groups('cooking_class:read')]
    private $startTime;

    #[ORM\Column(type: 'integer')]
    #[Groups('cooking_class:read')]
    private $duration; // durée en minutes

    #[ORM\Column(type: 'integer')]
    #[Groups('cooking_class:read')]
    private $maxParticipants;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('cooking_class:read')]
    private $volunteer;

    #[ORM\Column(type: 'integer')]
    #[Groups('cooking_class:read')]
    private $bookingCount = 0;  // Nouveau champ pour le nombre de réservations

    #[ORM\OneToMany(mappedBy: 'cookingClass', targetEntity: Booking::class, cascade: ['remove'])]
    private $bookings;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups('cooking_class:read')]
    private $isValidated = false;  // Nouveau champ booléen isValidated

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->isValidated = false; // Initialisation par défaut à false
    }

    // Getters and setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getMaxParticipants(): ?int
    {
        return $this->maxParticipants;
    }

    public function setMaxParticipants(int $maxParticipants): self
    {
        $this->maxParticipants = $maxParticipants;

        return $this;
    }

    public function getVolunteer(): ?Utilisateurs
    {
        return $this->volunteer;
    }

    public function setVolunteer(?Utilisateurs $volunteer): self
    {
        $this->volunteer = $volunteer;

        return $this;
    }

    public function getBookingCount(): int
    {
        return $this->bookingCount;
    }

    public function incrementBookingCount(): self
    {
        $this->bookingCount++;
        return $this;
    }

    public function decrementBookingCount(): self
    {
        $this->bookingCount--;
        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setCookingClass($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getCookingClass() === $this) {
                $booking->setCookingClass(null);
            }
        }

        return $this;
    }

    #[Groups('cooking_class:read')]
    public function getAvailableSlots(): int
    {
        return $this->maxParticipants - $this->bookings->count();
    }

    public function decreaseAvailableSlots(): void
    {
        if ($this->availableSlots > 0) {
            $this->availableSlots--;
        }
    }

    public function getIsValidated(): bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): self
    {
        $this->isValidated = $isValidated;

        return $this;
    }
}
