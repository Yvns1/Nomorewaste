<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\DistributionRepository")]
class Distribution
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $date;

    #[ORM\Column(type: 'string', length: 255)]
    private $lieu;

    #[ORM\Column(type: 'integer')]
    private $capaciteMaximale;

    #[ORM\Column(type: 'integer')]
    private $nombreInscrits = 0;

    #[ORM\Column(type: 'string', length: 255, options: ['default' => 'En cours'])]
    private $status = 'En cours'; // Ajout du statut

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

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getCapaciteMaximale(): ?int
    {
        return $this->capaciteMaximale;
    }

    public function setCapaciteMaximale(int $capaciteMaximale): self
    {
        $this->capaciteMaximale = $capaciteMaximale;

        return $this;
    }

    public function getNombreInscrits(): ?int
    {
        return $this->nombreInscrits;
    }

    public function incrementerNombreInscrits(): self
    {
        $this->nombreInscrits++;

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
}
