<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\BenevolesRepository;
use App\Entity\Services;
use App\Entity\Utilisateurs;


#[ORM\Entity(repositoryClass: 'App\Repository\BenevolesRepository')]
#[ORM\Table(name: 'benevoles')]
class Benevoles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text', nullable: true)]
    private $skills;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Utilisateurs')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
    private $user;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'string', length: 255)]
    private $nom;

    #[ORM\Column(type: 'string', length: 255)]
    private $motDePasse;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $photoDeProfil;

    #[ORM\ManyToMany(targetEntity: Services::class, inversedBy: 'benevoles')]
    #[ORM\JoinTable(name: 'benevoles_services')]
    private $servicesProposes;

    #[ORM\ManyToMany(targetEntity: Services::class, inversedBy: 'benevoles')]
    #[ORM\JoinTable(name: 'benevoles_services_acceptes')]
    private $servicesAcceptes;

    public function __construct()
    {
        $this->servicesProposes = new ArrayCollection();
        $this->servicesAcceptes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSkills(): ?string
    {
        return $this->skills;
    }

    public function setSkills(?string $skills): self
    {
        $this->skills = $skills;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): self
    {
        $this->motDePasse = $motDePasse;

        return $this;
    }

    public function getPhotoDeProfil(): ?string
    {
        return $this->photoDeProfil;
    }

    public function setPhotoDeProfil(?string $photoDeProfil): self
    {
        $this->photoDeProfil = $photoDeProfil;

        return $this;
    }

    /**
     * @return Collection|Services[]
     */
    public function getServicesProposes(): Collection
    {
        return $this->servicesProposes;
    }

    public function addServicesPropose(Services $servicesPropose): self
    {
        if (!$this->servicesProposes->contains($servicesPropose)) {
            $this->servicesProposes[] = $servicesPropose;
        }

        return $this;
    }

    public function removeServicesPropose(Services $servicesPropose): self
    {
        $this->servicesProposes->removeElement($servicesPropose);

        return $this;
    }

    /**
     * @return Collection|Services[]
     */
    public function getServicesAcceptes(): Collection
    {
        return $this->servicesAcceptes;
    }

    public function addServicesAccepte(Services $servicesAccepte): self
    {
        if (!$this->servicesAcceptes->contains($servicesAccepte)) {
            $this->servicesAcceptes[] = $servicesAccepte;
        }

        return $this;
    }

    public function removeServicesAccepte(Services $servicesAccepte): self
    {
        $this->servicesAcceptes->removeElement($servicesAccepte);

        return $this;
    }
}
