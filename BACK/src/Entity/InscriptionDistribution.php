<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\InscriptionDistributionRepository")]
class InscriptionDistribution
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nomParticipant;

    #[ORM\Column(type: 'string', length: 255)]
    private $emailParticipant;

    #[ORM\Column(type: 'string', length: 255)]
    private $telephoneParticipant;

    #[ORM\ManyToOne(targetEntity: Distribution::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $distribution;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomParticipant(): ?string
    {
        return $this->nomParticipant;
    }

    public function setNomParticipant(string $nomParticipant): self
    {
        $this->nomParticipant = $nomParticipant;

        return $this;
    }

    public function getEmailParticipant(): ?string
    {
        return $this->emailParticipant;
    }

    public function setEmailParticipant(string $emailParticipant): self
    {
        $this->emailParticipant = $emailParticipant;

        return $this;
    }

    public function getTelephoneParticipant(): ?string
    {
        return $this->telephoneParticipant;
    }

    public function setTelephoneParticipant(string $telephoneParticipant): self
    {
        $this->telephoneParticipant = $telephoneParticipant;

        return $this;
    }

    public function getDistribution(): ?Distribution
    {
        return $this->distribution;
    }

    public function setDistribution(?Distribution $distribution): self
    {
        $this->distribution = $distribution;

        return $this;
    }
}
