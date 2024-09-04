<?php

namespace App\Entity;

use App\Repository\PaiementsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementsRepository::class)]
class Paiements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private $amount;

    #[ORM\Column(type: 'date')]
    private $paymentDate;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $paymentMethod;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $status;

    #[ORM\ManyToOne(targetEntity: Factures::class)]
    private $invoice;

    // Getters and setters...
}
