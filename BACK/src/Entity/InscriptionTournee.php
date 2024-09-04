<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\InscriptionTourneeRepository")]
class InscriptionTournee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nomCommercant;

    #[ORM\Column(type: 'string', length: 255)]
    private $emailCommercant;

    #[ORM\Column(type: 'string', length: 255)]
    private $telephoneCommercant;

    #[ORM\ManyToOne(targetEntity: Tournee::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $tournee;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCommercant(): ?string
    {
        return $this->nomCommercant;
    }

    public function setNomCommercant(string $nomCommercant): self
    {
        $this->nomCommercant = $nomCommercant;
        return $this;
    }

    public function getEmailCommercant(): ?string
    {
        return $this->emailCommercant;
    }

    public function setEmailCommercant(string $emailCommercant): self
    {
        $this->emailCommercant = $emailCommercant;
        return $this;
    }

    public function getTelephoneCommercant(): ?string
    {
        return $this->telephoneCommercant;
    }

    public function setTelephoneCommercant(string $telephoneCommercant): self
    {
        $this->telephoneCommercant = $telephoneCommercant;
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

    public function getUser(): ?Utilisateurs
    {
        return $this->user;
    }

    public function setUser(?Utilisateurs $user): self
    {
        $this->user = $user;
        return $this;
    }
}
