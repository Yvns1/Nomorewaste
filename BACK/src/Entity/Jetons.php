<?php

namespace App\Entity;

use App\Repository\JetonsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JetonsRepository::class)]
class Jetons
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $token;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $expiresAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $type;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    private $user;

    // Getters and setters...
}
