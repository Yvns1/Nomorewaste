<?php

namespace App\Controller;

use App\Entity\InscriptionTournee;
use App\Entity\Tournee;
use App\Entity\Utilisateurs;
use App\Repository\TourneeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class InscriptionTourneeController extends AbstractController
{
    #[Route('/api/tournees/inscription', name: 'inscription_tournee', methods: ['POST'])]
    public function inscrire(Request $request, EntityManagerInterface $entityManager, TourneeRepository $tourneeRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérification que la tournée existe
        $tournee = $tourneeRepository->find($data['tourneeId']);

        if (!$tournee) {
            return $this->json(['error' => 'Tournee not found'], 404);
        }

        // Vérification que la capacité maximale n'est pas atteinte
        if ($tournee->getNombreInscrits() >= $tournee->getCapaciteMaximale()) {
            return $this->json(['error' => 'Tournee is full'], 400);
        }

        // Création de l'inscription
        $inscription = new InscriptionTournee();
        $inscription->setNomCommercant($data['nom']);
        $inscription->setEmailCommercant($data['email']);
        $inscription->setTelephoneCommercant($data['telephone']);
        $inscription->setTournee($tournee);
          // Lien avec l'utilisateur connecté
          // Lien avec l'utilisateur connecté
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'User not authenticated'], 401);
        }
        $inscription->setUser($user);

          $entityManager->persist($inscription);
  
          // Incrémenter le nombre d'inscrits pour la tournée
          $tournee->incrementerNombreInscrits();
          $entityManager->persist($tournee);
  
          // Sauvegarde en base de données
          $entityManager->flush();
  
          return $this->json(['status' => 'Inscription réussie!'], 201);
      }
}
