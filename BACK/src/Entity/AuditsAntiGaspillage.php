<?php

namespace App\Entity;

use App\Repository\AuditsAntiGaspillageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuditsAntiGaspillageRepository::class)]
class AuditsAntiGaspillage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $companyName;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $contactName;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $email;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $phone;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $businessType;

    #[ORM\Column(type: 'text', nullable: true)]
    private $currentChallenges;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    // Getters and setters...
}
