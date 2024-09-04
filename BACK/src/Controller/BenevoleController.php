<?php

namespace App\Controller;

use App\Entity\Services;
use App\Entity\Commentaire;
use App\Entity\Utilisateurs;
use App\Repository\ServicesRepository;
use App\Repository\CommentaireRepository;
use App\Repository\BenevolesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route("/api/benevole", name:"api_benevole_")]
 
class BenevoleController extends AbstractController
{
    private $entityManager;
    private $servicesRepository;
    private $commentaireRepository;
    private $benevolesRepository;

    public function __construct(EntityManagerInterface $entityManager, ServicesRepository $servicesRepository, CommentaireRepository $commentaireRepository, BenevolesRepository $benevolesRepository)
    {
        $this->entityManager = $entityManager;
        $this->servicesRepository = $servicesRepository;
        $this->commentaireRepository = $commentaireRepository;
        $this->benevolesRepository = $benevolesRepository;
    }

    #[Route('/services', name: 'list_services', methods: ['GET'])]
    public function listServices(): JsonResponse
    {
        // Utilisez une méthode du repository pour récupérer uniquement les services validés
        $services = $this->servicesRepository->findBy(['isValidated' => true]);

        // Mapper les données pour les retourner en réponse JSON
        $data = array_map(function (Services $service) {
            return [
                'id' => $service->getId(),
                'type' => $service->getType(),
                'description' => $service->getDescription(),
                'availability' => $service->getAvailability(),
                'isValidated' => $service->getIsValidated(), // Cela devrait toujours être 'true' ici
                'user' => $service->getUser() ? $service->getUser()->getId() : null 
            ];
        }, $services);

        return new JsonResponse($data);
    }

    #[Route('/services/{id}/subscribe', name: 'subscribe_service', methods: ['POST'])]
    public function subscribeService(int $id, Request $request): JsonResponse
    {
        $service = $this->servicesRepository->find($id);
        if (!$service) {
            return new JsonResponse(['status' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }
    
        // Récupérer l'utilisateur connecté via la requête
        $data = json_decode($request->getContent(), true);
        $userId = $data['userId'] ?? null;  // On récupère l'userId envoyé
    
        if (!$userId) {
            return new JsonResponse(['status' => 'User ID not provided'], Response::HTTP_BAD_REQUEST);
        }
    
        $user = $this->entityManager->getRepository(Utilisateurs::class)->find($userId);
        if (!$user) {
            return new JsonResponse(['status' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
    
        // Récupérer l'objet Benevole lié à cet utilisateur
        $benevole = $this->benevolesRepository->findOneBy(['user' => $user]);
    
        if (!$benevole) {
            return new JsonResponse(['status' => 'Benevole not found for this user'], Response::HTTP_NOT_FOUND);
        }
    
        // Vérifier que le bénévole n'est pas déjà inscrit à ce service
        if (!$service->getBenevoles()->contains($benevole)) {
            $service->addBenevole($benevole);
            $this->entityManager->flush();
            return new JsonResponse(['status' => 'Subscribed to service']);
        }
    
        return new JsonResponse(['status' => 'Already subscribed'], Response::HTTP_CONFLICT);
    }

    #[Route('/services/{id}/unsubscribe', name: 'unsubscribe_service', methods: ['POST'])]
    public function unsubscribeService(int $id): JsonResponse
    {
        $service = $this->servicesRepository->find($id);
        if (!$service) {
            return new JsonResponse(['status' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['status' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        // Récupérer l'objet Benevole lié à cet utilisateur
        $benevole = $this->benevolesRepository->findOneBy(['user' => $user]);

        if (!$benevole) {
            return new JsonResponse(['status' => 'Benevole not found for this user'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier si le bénévole est bien inscrit à ce service
        if ($service->getBenevoles()->contains($benevole)) {
            $service->removeBenevole($benevole);
            $this->entityManager->flush();
            return new JsonResponse(['status' => 'Unsubscribed from service']);
        }

        return new JsonResponse(['status' => 'Not subscribed'], Response::HTTP_CONFLICT);
    }

    #[Route('/services/inscrits', name: 'list_inscribed_services', methods: ['GET'])]
public function listInscribedServices(): JsonResponse
{
    $benevole = $this->getUser();
    if (!$benevole) {
        return new JsonResponse(['status' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
    }

    $services = $benevole->getServicesAcceptes(); // Obtenez les services auxquels le bénévole est inscrit

    $data = array_map(function (Services $service) {
        return [
            'id' => $service->getId(),
            'type' => $service->getType(),
            'description' => $service->getDescription(),
            'availability' => $service->getAvailability(),
            'isValidated' => $service->getIsValidated(), // Cela devrait être 'true' ou 'false'
            'user' => $service->getUser() ? $service->getUser()->getId() : null
        ];
    }, $services->toArray());

    return new JsonResponse($data);
}

#[Route('/mes-services', name: 'my_services', methods: ['GET'])]
public function getMyServices(): JsonResponse
{
    $benevole = $this->getUser();

    // Récupérez les services créés par l'utilisateur
    $createdServices = $this->servicesRepository->findBy(['user' => $benevole]);

    // Récupérez les services auxquels l'utilisateur est inscrit
    $subscribedServices = [];
    foreach ($createdServices as $service) {
        if ($service->getBénévoles()->contains($benevole)) {
            $subscribedServices[] = $service;
        }
    }

    $data = array_map(function (Services $service) {
        return [
            'id' => $service->getId(),
            'type' => $service->getType(),
            'description' => $service->getDescription(),
            'availability' => $service->getAvailability(),
            'isValidated' => $service->getIsValidated(),
            'user' => $service->getUser() ? $service->getUser()->getId() : null 
        ];
    }, $subscribedServices);

    return new JsonResponse($data);
}



    #[Route('/services', name: 'create_service', methods: ['POST'])]
    public function createService(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (empty($data['type']) || empty($data['description']) || empty($data['availability'])) {
            return new JsonResponse(['status' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        $service = new Services();
        $service->setType($data['type']);
        $service->setDescription($data['description']);
        $service->setAvailability($data['availability']);
        $service->setUser($this->getUser()); // assuming user is already authenticated
        $this->entityManager->persist($service);
        $this->entityManager->flush();
        
        return new JsonResponse(['status' => 'Service created', 'id' => $service->getId()]);
    }

    #[Route('/services/{id}', name: 'update_service', methods: ['PUT'])]
    public function updateService(Request $request, int $id): JsonResponse
    {
        $service = $this->servicesRepository->find($id);
        if (!$service) {
            return new JsonResponse(['status' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }

        if ($this->getUser() !== $service->getUser()) {
            return new JsonResponse(['status' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);
        $service->setType($data['type']);
        $service->setDescription($data['description']);
        $service->setAvailability($data['availability']);
        $this->entityManager->flush();
        
        return new JsonResponse(['status' => 'Service updated']);
    }

    #[Route('/services/{id}/delete', name: 'delete_service', methods: ['DELETE'])]
    public function deleteService(int $id): JsonResponse
    {
        $service = $this->servicesRepository->find($id);
        if (!$service) {
            return new JsonResponse(['status' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }

        if ($this->getUser() !== $service->getUser()) {
            return new JsonResponse(['status' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }
        $this->entityManager->remove($service);
        $this->entityManager->flush();
        
        return new JsonResponse(['status' => 'Service deleted']);
    }

    #[Route('/commentaires', name: 'list_commentaires', methods: ['GET'])]
    public function listCommentaires(): JsonResponse
    {
        $commentaires = $this->commentaireRepository->findAll();
        $data = array_map(function (Commentaire $commentaire) {
            return [
                'id' => $commentaire->getId(),
                'content' => $commentaire->getContent(),
                'user' => $commentaire->getUser()->getId(),
                'service' => $commentaire->getService()->getId()
            ];
        }, $commentaires);

        return new JsonResponse($data);
    }

    #[Route('/commentaires', name: 'create_commentaire', methods: ['POST'])]
    public function createCommentaire(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (empty($data['content']) || empty($data['service'])) {
            return new JsonResponse(['status' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        $commentaire = new Commentaire();
        $commentaire->setContent($data['content']);
        $commentaire->setUser($this->getUser()); // assuming user is already authenticated
        $service = $this->servicesRepository->find($data['service']);
        if (!$service) {
            return new JsonResponse(['status' => 'Invalid service ID'], Response::HTTP_BAD_REQUEST);
        }
        $commentaire->setService($service);
        $this->entityManager->persist($commentaire);
        $this->entityManager->flush();
        
        return new JsonResponse(['status' => 'Commentaire created', 'id' => $commentaire->getId()]);
    }

    #[Route('/commentaires/{id}', name: 'update_commentaire', methods: ['PUT'])]
    public function updateCommentaire(Request $request, int $id): JsonResponse
    {
        $commentaire = $this->commentaireRepository->find($id);
        if (!$commentaire) {
            return new JsonResponse(['status' => 'Commentaire not found'], Response::HTTP_NOT_FOUND);
        }

        if ($this->getUser() !== $commentaire->getUser()) {
            return new JsonResponse(['status' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }
        
        $data = json_decode($request->getContent(), true);
        $commentaire->setContent($data['content']);
        $this->entityManager->flush();
        
        return new JsonResponse(['status' => 'Commentaire updated']);
    }

    #[Route('/commentaires/{id}/delete', name: 'delete_commentaire', methods: ['DELETE'])]
    public function deleteCommentaire(int $id): JsonResponse
    {
        $commentaire = $this->commentaireRepository->find($id);
        if (!$commentaire) {
            return new JsonResponse(['status' => 'Commentaire not found'], Response::HTTP_NOT_FOUND);
        }

        if ($this->getUser() !== $commentaire->getUser()) {
            return new JsonResponse(['status' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }
        
        $this->entityManager->remove($commentaire);
        $this->entityManager->flush();
        
        return new JsonResponse(['status' => 'Commentaire deleted']);
    }
}
