<?php

// src/Entity/Utilisateurs.php

namespace App\Entity;

use App\Repository\UtilisateursRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UtilisateursRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Utilisateurs implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['cooking_class:read', 'user:read'])]
    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups(['cooking_class:read', 'user:read'])]
    private $email;

    #[ORM\Column(type: 'string', length: 255)]
    private $password;

    #[Groups(['cooking_class:read', 'user:read'])]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $userType;

    #[ORM\Column(type: 'text', nullable: true)]
    private $additionalInfo;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $updatedAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $token;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Adhesion::class)]
    private $adhesions;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Benevoles::class)]
    private $benevoles;

    #[ORM\Column(type: 'string', length: 255, options: ['default' => 'active'])]
    private $etat = 'active'; // Ajouter cette propriété

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Services::class)]
    private $servicesProposes;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->adhesions = new ArrayCollection();
        $this->benevoles = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getUserType(): ?string
    {
        return $this->userType;
    }

    public function setUserType(?string $userType): self
    {
        $this->userType = $userType;
        return $this;
    }

    public function getAdditionalInfo(): ?string
    {
        return $this->additionalInfo;
    }

    public function setAdditionalInfo(?string $additionalInfo): self
    {
        $this->additionalInfo = $additionalInfo;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;
        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;
        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        // Suppression des informations sensibles temporaires si nécessaire
    }

    public function getSalt(): ?string
    {
        return null; // Non nécessaire pour bcrypt ou sodium
    }

    public function getAdhesions(): Collection
    {
        return $this->adhesions;
    }

    public function addAdhesion(Adhesion $adhesion): self
    {
        if (!$this->adhesions->contains($adhesion)) {
            $this->adhesions[] = $adhesion;
            $adhesion->setUser($this);
        }

        return $this;
    }

    public function removeAdhesion(Adhesion $adhesion): self
    {
        if ($this->adhesions->removeElement($adhesion)) {
            if ($adhesion->getUser() === $this) {
                $adhesion->setUser(null);
            }
        }

        return $this;
    }

    public function getBenevoles(): Collection
    {
        return $this->benevoles;
    }

    public function addBenevole(Benevoles $benevole): self
    {
        if (!$this->benevoles->contains($benevole)) {
            $this->benevoles[] = $benevole;
            $benevole->setUser($this);
        }

        return $this;
    }

    public function removeBenevole(Benevoles $benevole): self
    {
        if ($this->benevoles->removeElement($benevole)) {
            if ($benevole->getUser() === $this) {
                $benevole->setUser(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->createdAt ? $this->createdAt->format('Y-m-d H:i:s') : '';
    }
}
