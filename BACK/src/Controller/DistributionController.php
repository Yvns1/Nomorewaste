<?php

namespace App\Controller;

use App\Entity\Distribution;
use App\Entity\InscriptionDistribution;
use App\Repository\DistributionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DistributionController extends AbstractController
{
    #[Route('/api/distributions', name: 'get_distributions', methods: ['GET'])]
    public function getDistributions(DistributionRepository $distributionRepository): JsonResponse
    {
        $distributions = $distributionRepository->findAll();
        
        $data = [];
        foreach ($distributions as $distribution) {
            $data[] = [
                'id' => $distribution->getId(),
                'date' => $distribution->getDate()->format('Y-m-d H:i:s'),
                'lieu' => $distribution->getLieu(),
                'capaciteMaximale' => $distribution->getCapaciteMaximale(),
                'nombreInscrits' => $distribution->getNombreInscrits(),
                'status' => $distribution->getStatus()
            ];
        }

        return $this->json($data);
    }

    #[Route('/api/distribution/inscription', name: 'inscription_distribution', methods: ['POST'])]
    public function inscrire(Request $request, EntityManagerInterface $entityManager, DistributionRepository $distributionRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $distribution = $distributionRepository->find($data['distributionId']);

        if (!$distribution) {
            return $this->json(['error' => 'Distribution not found'], 404);
        }

        if ($distribution->getNombreInscrits() >= $distribution->getCapaciteMaximale()) {
            return $this->json(['error' => 'Distribution is full'], 400);
        }

        $inscription = new InscriptionDistribution();
        $inscription->setNomParticipant($data['nom']);
        $inscription->setEmailParticipant($data['email']);
        $inscription->setTelephoneParticipant($data['telephone']);
        $inscription->setDistribution($distribution);

        $entityManager->persist($inscription);

        $distribution->incrementerNombreInscrits();
        $entityManager->persist($distribution);
        $entityManager->flush();

        return $this->json(['status' => 'Inscription réussie!'], 201);
    }

    #[Route('/api/distributions', name: 'create_distribution', methods: ['POST'])]
    public function createDistribution(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $distribution = new Distribution();
        $distribution->setDate(new \DateTime($data['date']));
        $distribution->setLieu($data['lieu']);
        $distribution->setCapaciteMaximale($data['capaciteMaximale']);

        $entityManager->persist($distribution);
        $entityManager->flush();

        return $this->json(['status' => 'Distribution créée avec succès !'], 201);
    }

    #[Route('/api/distributions/{id}', name: 'edit_distribution', methods: ['PUT'])]
    public function editDistribution($id, Request $request, EntityManagerInterface $entityManager, DistributionRepository $distributionRepository): JsonResponse
    {
        $distribution = $distributionRepository->find($id);
        if (!$distribution) {
            return $this->json(['error' => 'Distribution not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        // Mise à jour des champs de la distribution
        if (isset($data['date'])) {
            $distribution->setDate(new \DateTime($data['date']));
        }
        if (isset($data['lieu'])) {
            $distribution->setLieu($data['lieu']);
        }
        if (isset($data['capaciteMaximale'])) {
            $distribution->setCapaciteMaximale($data['capaciteMaximale']);
        }
        if (isset($data['status'])) {
            $distribution->setStatus($data['status']);
        }

        $entityManager->persist($distribution);
        $entityManager->flush();

        return $this->json(['status' => 'Distribution modifiée avec succès !'], 200);
    }

    #[Route('/api/distributions/{id}', name: 'delete_distribution', methods: ['DELETE'])]
    public function deleteDistribution($id, EntityManagerInterface $entityManager, DistributionRepository $distributionRepository): JsonResponse
    {
        $distribution = $distributionRepository->find($id);
        if (!$distribution) {
            return $this->json(['error' => 'Distribution not found'], 404);
        }

        $entityManager->remove($distribution);
        $entityManager->flush();

        return $this->json(['status' => 'Distribution supprimée avec succès !'], 200);
    }
}
