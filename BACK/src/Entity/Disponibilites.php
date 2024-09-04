<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class Disponibilites
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $debut;

    #[ORM\Column(type: 'datetime')]
    private $fin;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    private $commercant;

    #[ORM\ManyToOne(targetEntity: Tournee::class, inversedBy: 'disponibilites')]
    private $tournee;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDebut(): ?\DateTimeInterface
    {
        return $this->debut;
    }

    public function setDebut(\DateTimeInterface $debut): self
    {
        $this->debut = $debut;
        return $this;
    }

    public function getFin(): ?\DateTimeInterface
    {
        return $this->fin;
    }

    public function setFin(\DateTimeInterface $fin): self
    {
        $this->fin = $fin;
        return $this;
    }

    public function getCommercant(): ?Utilisateurs
    {
        return $this->commercant;
    }

    public function setCommercant(?Utilisateurs $commercant): self
    {
        $this->commercant = $commercant;
        return $this;
    }

    public function getTournee(): ?Tournee
    {
        return $this->tournee;
    }

    public function setTournee(?Tournee $tournee): self
    {
        $this->tournee = $tournee;
        return $this;
    }
}
