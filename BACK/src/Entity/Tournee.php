<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\TourneeRepository")]
class Tournee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $date;

    #[ORM\Column(type: 'string', length: 255)]
    private $status;

    #[ORM\Column(type: 'string', length: 255)]  
    private $zone;

    #[ORM\Column(type: 'string', length: 255)]  
    private $adresse;

    #[ORM\Column(type: 'integer')]
    private $capaciteMaximale = 10;  // Capacité maximale par défaut

    #[ORM\Column(type: 'integer')]
    private $nombreInscrits = 0;  // Nombre d'inscrits initialisé à 0

    #[ORM\ManyToMany(targetEntity: Utilisateurs::class)]
    private $commercants;

    #[ORM\OneToMany(targetEntity: Disponibilites::class, mappedBy: 'tournee')]
    private $disponibilites;

    public function __construct()
    {
        $this->commercants = new ArrayCollection();
        $this->disponibilites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getZone(): ?string
    {
        return $this->zone;
    }

    public function setZone(string $zone): self
    {
        $this->zone = $zone;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getCapaciteMaximale(): int
    {
        return $this->capaciteMaximale;
    }

    public function setCapaciteMaximale(int $capaciteMaximale): self
    {
        $this->capaciteMaximale = $capaciteMaximale;
        return $this;
    }

    public function getNombreInscrits(): int
    {
        return $this->nombreInscrits;
    }

    public function setNombreInscrits(int $nombreInscrits): self
    {
        $this->nombreInscrits = $nombreInscrits;
        return $this;
    }

    public function incrementerNombreInscrits(): self
    {
        $this->nombreInscrits++;
        return $this;
    }

    public function isFull(): bool
    {
        return $this->nombreInscrits >= $this->capaciteMaximale;
    }

    public function getCommercants(): Collection
    {
        return $this->commercants;
    }

    public function addCommercant(Utilisateurs $commercant): self
    {
        if (!$this->commercants->contains($commercant)) {
            $this->commercants[] = $commercant;
            $this->incrementerNombreInscrits(); // Incrémente le nombre d'inscrits
        }
        return $this;
    }

    public function removeCommercant(Utilisateurs $commercant): self
    {
        if ($this->commercants->removeElement($commercant)) {
            $this->nombreInscrits--; // Décrémente le nombre d'inscrits
        }
        return $this;
    }

    public function getDisponibilites(): Collection
    {
        return $this->disponibilites;
    }

    public function addDisponibilite(Disponibilites $disponibilite): self
    {
        if (!$this->disponibilites->contains($disponibilite)) {
            $this->disponibilites[] = $disponibilite;
            $disponibilite->setTournee($this);
        }
        return $this;
    }

    public function removeDisponibilite(Disponibilites $disponibilite): self
    {
        if ($this->disponibilites->removeElement($disponibilite)) {
            if ($disponibilite->getTournee() === $this) {
                $disponibilite->setTournee(null);
            }
        }
        return $this;
    }
}
