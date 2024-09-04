<?php

namespace App\Entity;

use App\Repository\ToursRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ToursRepository::class)]
class Tours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'date')]
    private $date;

    #[ORM\Column(type: 'time', nullable: true)]
    private $time;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $startLocation;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $endLocation;

    #[ORM\Column(type: 'text', nullable: true)]
    private $assignedVolunteers;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $status;

    // Getters and setters...
}
