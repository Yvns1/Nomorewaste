<?php

namespace App\Entity;

use App\Repository\MessagesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessagesRepository::class)]
class Messages
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $content;

    #[ORM\Column(type: 'datetime_immutable')]
    private $sentAt;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    private $sender;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    private $recipient;

    // Getters and setters...
}
