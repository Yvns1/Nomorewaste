<?php

// src/Controller/StockController.php

namespace App\Controller;

use App\Entity\FrigosIntelligents;
use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class StockController extends AbstractController
{
    #[Route('/api/stocks', name: 'list_stocks', methods: ['GET'])]
    public function listStocks(EntityManagerInterface $entityManager): JsonResponse
    {
        $stocks = $entityManager->getRepository(FrigosIntelligents::class)->findAll();

        $data = [];
        foreach ($stocks as $stock) {
            $data[] = [
                'id' => $stock->getId(),
                'name' => $stock->getName(),
                'quantity' => $stock->getQuantity(),
                'category' => $stock->getCategory(),
                'subCategory' => $stock->getSubCategory(),
                'unit' => $stock->getUnit(),
                'provenance' => $stock->getProvenance(),
                'dateAjout' => $stock->getDateAjout()->format('Y-m-d'),
                'status' => $stock->getStatus(), // Ajout du statut
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/api/stocks', name: 'creation_stock', methods: ['POST'])]
    public function creationStock(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérification et récupération de l'user_id dans la requête
        if (!isset($data['user_id'])) {
            return new JsonResponse(['error' => 'User ID manquant dans la requête'], Response::HTTP_BAD_REQUEST);
        }

        $user = $entityManager->getRepository(Utilisateurs::class)->find($data['user_id']);

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Création d'un nouveau stock
        $stock = new FrigosIntelligents();
        $stock->setName($data['name']);
        $stock->setQuantity($data['quantity']);
        $stock->setCategory($data['category']);
        $stock->setSubCategory($data['subCategory'] ?? null);
        $stock->setUnit($data['unit']);
        $stock->setProvenance($data['provenance']);
        $stock->setDateAjout(new \DateTime($data['dateAjout'])); // Ajout de la date
        $stock->setStatus($data['status'] ?? 'Pending'); // Statut par défaut : En attente

        // Association du stock avec l'utilisateur récupéré
        $stock->setUser($user);

        // Persistance du stock en base de données
        $entityManager->persist($stock);
        $entityManager->flush();

        return new JsonResponse(['status' => 'Product added to stock'], Response::HTTP_CREATED);
    }
    
    #[Route('/api/stocks/{id}', name: 'delete_stock', methods: ['DELETE'])]
    public function deleteStock(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $stock = $entityManager->getRepository(FrigosIntelligents::class)->find($id);

        if (!$stock) {
            return new JsonResponse(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($stock);
        $entityManager->flush();

        return new JsonResponse(['status' => 'Product removed from stock'], Response::HTTP_OK);
    }

    #[Route('/api/stocks/{id}/received', name: 'mark_stock_received', methods: ['PATCH'])]
public function markAsReceived(int $id, EntityManagerInterface $entityManager): JsonResponse
{
    $stock = $entityManager->getRepository(FrigosIntelligents::class)->find($id);

    if (!$stock) {
        return new JsonResponse(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
    }

    $stock->setStatus('En stock'); // Changer le statut à "En stock"
    $entityManager->flush();

    return new JsonResponse(['status' => 'Product marked as received and added to stock'], Response::HTTP_OK);
}


  
    #[Route('/api/stocks/{id}/status', name: 'update_stock_status', methods: ['PATCH'])]
    public function updateStatus(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $stock = $entityManager->getRepository(FrigosIntelligents::class)->find($id);

        if (!$stock) {
            return new JsonResponse(['error' => 'Stock not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $status = $data['status'] ?? null;

        if (!$status) {
            return new JsonResponse(['error' => 'Status is required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $stock->setStatus($status);
        $entityManager->flush();

        return new JsonResponse(['status' => 'Stock status updated successfully'], JsonResponse::HTTP_OK);
    }
}
