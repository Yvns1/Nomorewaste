<?php

namespace App\Entity;

use App\Repository\CommercantsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommercantsRepository::class)]
class Commercants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $email;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $address;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $phone;

    #[ORM\Column(type: 'datetime_immutable')]
    private $registrationDate;

    #[ORM\Column(type: 'text', nullable: true)]
    private $additionalInfo;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    private $user;

    // Getters and setters...
}
