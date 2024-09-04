<?php

namespace App\Controller;

use App\Entity\FrigosIntelligents;
use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DonController extends AbstractController
{
    #[Route('/api/dons/new', name: 'don_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Récupération de l'utilisateur à partir de l'ID utilisateur fourni dans les données de la requête
        if (!isset($data['user_id'])) {
            return new JsonResponse(['error' => 'User ID manquant dans la requête'], Response::HTTP_BAD_REQUEST);
        }

        $user = $entityManager->getRepository(Utilisateurs::class)->find($data['user_id']);

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Validation des autres données reçues
        if (!isset($data['name'], $data['quantity'], $data['category'], $data['unit'], $data['status'])) {
            return new JsonResponse(['error' => 'Données manquantes ou invalides'], Response::HTTP_BAD_REQUEST);
        }

        // Création d'un nouveau don
        $don = new FrigosIntelligents();
        $don->setName($data['name']);
        $don->setQuantity($data['quantity']);
        $don->setCategory($data['category']);
        $don->setSubCategory($data['subCategory'] ?? null);
        $don->setUnit($data['unit']);
        $don->setProvenance($user->getName());
        $don->setDateAjout(new \DateTime());
        $don->setUser($user);
        $don->setStatus($data['status']); // Définit le statut initial du don

        $entityManager->persist($don);
        $entityManager->flush();

        return new JsonResponse(['status' => 'Don ajouté avec succès'], Response::HTTP_CREATED);
    }

    #[Route('/api/dons', name: 'dons', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupération de l'utilisateur authentifié
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], Response::HTTP_UNAUTHORIZED);
        }

        // Récupération des dons de l'utilisateur authentifié
        $dons = $entityManager->getRepository(FrigosIntelligents::class)->findBy(['user' => $user]);

        $data = array_map(function (FrigosIntelligents $don) {
            return [
                'id' => $don->getId(),
                'name' => $don->getName(),
                'quantity' => $don->getQuantity(),
                'category' => $don->getCategory(),
                'subCategory' => $don->getSubCategory(),
                'unit' => $don->getUnit(),
                'provenance' => $don->getProvenance(),
                'dateAjout' => $don->getDateAjout()->format(\DateTime::ISO8601),
                'status' => $don->getStatus(), // Statut du don
            ];
        }, $dons);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/api/dons/user/{userId}', name: 'don_user_list', methods: ['GET'])]
    public function getUserDons(int $userId, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupération de l'utilisateur par ID
        $user = $entityManager->getRepository(Utilisateurs::class)->find($userId);

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Récupération des dons associés à cet utilisateur
        $dons = $entityManager->getRepository(FrigosIntelligents::class)->findBy(['user' => $user]);

        $data = array_map(function (FrigosIntelligents $don) {
            return [
                'id' => $don->getId(),
                'name' => $don->getName(),
                'quantity' => $don->getQuantity(),
                'category' => $don->getCategory(),
                'subCategory' => $don->getSubCategory(),
                'unit' => $don->getUnit(),
                'provenance' => $don->getProvenance(),
                'dateAjout' => $don->getDateAjout()->format(\DateTime::ISO8601),
                'status' => $don->getStatus(), // Statut du don
            ];
        }, $dons);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/api/dons/{id}/status', name: 'update_don_status', methods: ['PATCH'])]
    public function updateStatus(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['status'])) {
            return new JsonResponse(['error' => 'Statut manquant'], Response::HTTP_BAD_REQUEST);
        }

        $don = $entityManager->getRepository(FrigosIntelligents::class)->find($id);

        if (!$don) {
            return new JsonResponse(['error' => 'Don non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $don->setStatus($data['status']);
        $entityManager->flush();

        return new JsonResponse(['status' => 'Statut mis à jour avec succès'], Response::HTTP_OK);
    }

    #[Route('/api/dons/{id}', name: 'don_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $don = $entityManager->getRepository(FrigosIntelligents::class)->find($id);

        if (!$don) {
            return new JsonResponse(['error' => 'Don non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($don);
        $entityManager->flush();

        return new JsonResponse(['status' => 'Don supprimé avec succès'], Response::HTTP_OK);
    }

    #[Route('/api/dons/{id}', name: 'get_don', methods: ['GET'])]
    public function getDon($id, EntityManagerInterface $entityManager): JsonResponse
    {
        // Validation du type de l'ID
        if (!is_numeric($id)) {
            return new JsonResponse(['error' => 'ID invalide'], Response::HTTP_BAD_REQUEST);
        }

        $id = (int) $id;

        // Récupération du don par ID
        $don = $entityManager->getRepository(FrigosIntelligents::class)->find($id);

        if (!$don) {
            return new JsonResponse(['error' => 'Don non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Préparation des données du don pour la réponse
        $data = [
            'id' => $don->getId(),
            'name' => $don->getName(),
            'quantity' => $don->getQuantity(),
            'category' => $don->getCategory(),
            'subCategory' => $don->getSubCategory(),
            'unit' => $don->getUnit(),
            'provenance' => $don->getProvenance(),
            'dateAjout' => $don->getDateAjout()->format(\DateTime::ISO8601),
            'status' => $don->getStatus(),
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

}
