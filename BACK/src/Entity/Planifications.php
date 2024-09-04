<?php

namespace App\Entity;

use App\Repository\PlanificationsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlanificationsRepository::class)]
class Planifications
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'date')]
    private $date;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $status;

    #[ORM\ManyToOne(targetEntity: Services::class)]
    private $service;

    #[ORM\ManyToOne(targetEntity: Benevoles::class)]
    private $volunteer;

    // Getters and setters...
}
