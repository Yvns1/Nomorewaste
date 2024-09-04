<?php

namespace App\Controller;

use App\Entity\Tournee;
use App\Entity\Utilisateurs;
use App\Entity\InscriptionTournee; // Assurez-vous d'avoir créé cette entité pour gérer les inscriptions
use App\Repository\TourneeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TourneeController extends AbstractController
{
    #[Route('/api/tournees', name: 'get_tournees', methods: ['GET'])]
    public function getTournees(Request $request, TourneeRepository $tourneeRepository): JsonResponse
    {
        $status = $request->query->get('status');
        $zone = $request->query->get('zone');
        $year = $request->query->get('year');
        $month = $request->query->get('month');

        $queryBuilder = $tourneeRepository->createQueryBuilder('t');

        if ($status) {
            $queryBuilder->andWhere('t.status = :status')
                         ->setParameter('status', $status);
        }

        if ($zone) {
            $queryBuilder->andWhere('t.zone = :zone')
                         ->setParameter('zone', $zone);
        }

        if ($year) {
            $queryBuilder->andWhere('YEAR(t.date) = :year')
                         ->setParameter('year', $year);
        }

        if ($month) {
            $queryBuilder->andWhere('MONTH(t.date) = :month')
                         ->setParameter('month', $month);
        }

        $tournees = $queryBuilder->getQuery()->getResult();

        return $this->json($tournees);
    }

    #[Route('/api/tournee', name: 'create_tournee', methods: ['POST'])]
    public function createTournee(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $tournee = new Tournee();
        $tournee->setDate(new \DateTime($data['date']));
        $tournee->setStatus($data['status']);
        $tournee->setZone($data['zone']);
        $tournee->setAdresse($data['adresse']);
        $tournee->setCapaciteMaximale = 10;  // Ajouter la capacité maximale

        $entityManager->persist($tournee);
        $entityManager->flush();

        return $this->json(['status' => 'Tournee created!'], 201);
    }

    #[Route('/api/tournee/{id}', name: 'update_tournee', methods: ['PUT'])]
    public function updateTournee(int $id, Request $request, EntityManagerInterface $entityManager, TourneeRepository $tourneeRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $tournee = $tourneeRepository->find($id);

        if (!$tournee) {
            return $this->json(['error' => 'Tournee not found'], 404);
        }

        if (isset($data['date'])) {
            $tournee->setDate(new \DateTime($data['date']));
        }
        if (isset($data['status'])) {
            $tournee->setStatus($data['status']);
        }
        if (isset($data['zone'])) {
            $tournee->setZone($data['zone']);
        }
        if (isset($data['adresse'])) {
            $tournee->setAdresse($data['adresse']);
        }
        if (isset($data['capaciteMaximale'])) {
            $tournee->setCapaciteMaximale($data['capaciteMaximale']);
        }

        $entityManager->flush();

        return $this->json(['status' => 'Tournee updated!'], 200);
    }

    #[Route('/api/tournee/{id}', name: 'delete_tournee', methods: ['DELETE'])]
    public function deleteTournee(int $id, EntityManagerInterface $entityManager, TourneeRepository $tourneeRepository): JsonResponse
    {
        $tournee = $tourneeRepository->find($id);

        if (!$tournee) {
            return $this->json(['error' => 'Tournee not found'], 404);
        }

        $entityManager->remove($tournee);
        $entityManager->flush();

        return $this->json(['status' => 'Tournee deleted!'], 200);
    }

    #[Route('/api/tournee/{id}', name: 'get_tournee', methods: ['GET'])]
    public function getTournee(int $id, TourneeRepository $tourneeRepository): JsonResponse
    {
        $tournee = $tourneeRepository->find($id);

        if (!$tournee) {
            return $this->json(['error' => 'Tournee not found'], 404);
        }

        return $this->json($tournee);
    }

    #[Route('/api/inscription-tournee', name: 'inscription_tournee', methods: ['POST'])]
    public function inscrire(Request $request, EntityManagerInterface $entityManager, TourneeRepository $tourneeRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        // Vérification que la clé 'tourneeId' est présente
        if (!isset($data['tourneeId'])) {
            return $this->json(['error' => 'tourneeId key is missing'], 400);
        }
    
        // Vérification que la clé 'userId' est présente
        if (!isset($data['userId'])) {
            return $this->json(['error' => 'userId key is missing'], 400);
        }
    
        // Récupérer la tournée
        $tournee = $tourneeRepository->find($data['tourneeId']);
        if (!$tournee) {
            return $this->json(['error' => 'Tournee not found'], 404);
        }
    
        // Vérification que la capacité maximale n'est pas atteinte
        if ($tournee->getNombreInscrits() >= $tournee->getCapaciteMaximale()) {
            return $this->json(['error' => 'Tournee is full'], 400);
        }
    
        // Récupérer l'utilisateur via son ID
        $user = $entityManager->getRepository(Utilisateurs::class)->find($data['userId']);
        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }
    
        // Création de l'inscription
        $inscription = new InscriptionTournee();
        $inscription->setNomCommercant($data['nom'] ?? '');
        $inscription->setEmailCommercant($data['email'] ?? '');
        $inscription->setTelephoneCommercant($data['telephone'] ?? '');
        $inscription->setTournee($tournee);
        $inscription->setUser($user);
    
        // Persister l'inscription
        $entityManager->persist($inscription);
    
        // Incrémenter le nombre d'inscrits pour la tournée
        $tournee->incrementerNombreInscrits();
        $entityManager->persist($tournee);
    
        // Sauvegarde en base de données
        $entityManager->flush();
    
        return $this->json(['status' => 'Inscription réussie!'], 201);
    }
    

}
