<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'App\Repository\ServicesRepository')]
class Services
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $type;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $availability;

    #[ORM\Column(type: 'boolean')]
    private $isValidated = false;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Utilisateurs', inversedBy: 'servicesProposes')]
    private $user;

    #[ORM\ManyToMany(targetEntity: 'App\Entity\Benevoles', mappedBy: 'servicesProposes')]
    private $benevoles;

    public function __construct()
    {
        $this->benevoles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getAvailability(): ?string
    {
        return $this->availability;
    }

    public function setAvailability(?string $availability): self
    {
        $this->availability = $availability;
        return $this;
    }

    public function getIsValidated(): ?bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): self
    {
        $this->isValidated = $isValidated;
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

    /**
     * @return Collection|Benevoles[]
     */
    public function getBenevoles(): Collection
    {
        return $this->benevoles;
    }

    public function addBenevole(Benevoles $benevole): self
    {
        if (!$this->benevoles->contains($benevole)) {
            $this->benevoles[] = $benevole;
            $benevole->addServicesPropose($this);
        }

        return $this;
    }

    public function removeBenevole(Benevoles $benevole): self
    {
        if ($this->benevoles->removeElement($benevole)) {
            $benevole->removeServicesPropose($this);
        }

        return $this;
    }
}
