<?php

namespace App\Controller;

use App\Entity\Services;
use App\Entity\Benevoles;
use App\Repository\ServicesRepository;
use App\Repository\BenevolesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route("/api/admin", name:"api_admin_")]

class AdminBenevoleController extends AbstractController
{
    private $entityManager;
    private $servicesRepository;
    private $benevolesRepository;

    public function __construct(EntityManagerInterface $entityManager, ServicesRepository $servicesRepository, BenevolesRepository $benevolesRepository)
    {
        $this->entityManager = $entityManager;
        $this->servicesRepository = $servicesRepository;
        $this->benevolesRepository = $benevolesRepository;
    }

    
    #[Route('/services', name: 'list_services', methods: ['GET'])]
    public function listServices(): JsonResponse
    {
    $services = $this->servicesRepository->findAll();
    $data = array_map(function (Services $service) {
        return [
            'id' => $service->getId(),
            'type' => $service->getType(),
            'description' => $service->getDescription(),
            'availability' => $service->getAvailability(),
            'isValidated' => $service->getIsValidated(),
            'user' => $service->getUser() ? $service->getUser()->getId() : null // Vérification si l'utilisateur est null
        ];
    }, $services);

    return new JsonResponse($data);
}


#[Route('/services/{id}', name: 'service_detail', methods: ['GET'])]
public function serviceDetail(Services $service = null): JsonResponse
{
    if ($service === null) {
        return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
    }

    return new JsonResponse([
        'id' => $service->getId(),
        'type' => $service->getType(),
        'description' => $service->getDescription(),
        'availability' => $service->getAvailability(),
        'isValidated' => $service->getIsValidated(),
        'user' => $service->getUser() ? $service->getUser()->getId() : null
    ]);
}

    #[Route('/services/{id}/validate', name: 'validate_service', methods: ['POST'])]
    public function validateService(Services $service): JsonResponse
    {
        $service->setIsValidated(true);
        $this->entityManager->flush();
        
        return new JsonResponse(['status' => 'Service validated']);
    }

    #[Route('/services/{id}/delete', name: 'delete_service', methods: ['DELETE'])]
    public function deleteService(Services $service): JsonResponse
    {
        $this->entityManager->remove($service);
        $this->entityManager->flush();
        
        return new JsonResponse(['status' => 'Service deleted']);
    }

    #[Route('/benevoles', name: 'list_benevoles', methods: ['GET'])]
    public function listBenevoles(): JsonResponse
    {
        $benevoles = $this->benevolesRepository->findAll();
        $data = array_map(function (Benevoles $benevole) {
            return [
                'id' => $benevole->getId(),
                'email' => $benevole->getEmail(),
                'nom' => $benevole->getNom(),
                'skills' => $benevole->getSkills(),
                'photoDeProfil' => $benevole->getPhotoDeProfil(),
            ];
        }, $benevoles);

        return new JsonResponse($data);
    }

    #[Route('/benevoles/{id}', name: 'benevole_detail', methods: ['GET'])]
    public function benevoleDetail(Benevoles $benevole): JsonResponse
    {
        // Construction de la réponse JSON avec les détails du bénévole
        return new JsonResponse([
            'id' => $benevole->getId(),
            'nom' => $benevole->getNom(),
            'email' => $benevole->getEmail(),
            'skills' => $benevole->getSkills(),
            'photoDeProfil' => $benevole->getPhotoDeProfil(),
            'servicesProposes' => array_map(function (Services $service) {
                return [
                    'id' => $service->getId(),
                    'type' => $service->getType(),
                    'description' => $service->getDescription(),
                    'availability' => $service->getAvailability(),
                    'isValidated' => $service->getIsValidated(),
                ];
            }, $benevole->getServicesProposes()->toArray()),  // Assurez-vous que cette relation est définie dans l'entité
            'servicesAcceptes' => array_map(function (Services $service) {
                return [
                    'id' => $service->getId(),
                    'type' => $service->getType(),
                    'description' => $service->getDescription(),
                    'availability' => $service->getAvailability(),
                    'isValidated' => $service->getIsValidated(),
                ];
            }, $benevole->getServicesAcceptes()->toArray()) // Assurez-vous que cette relation est définie dans l'entité
        ]);
    }
    

    #[Route('/benevoles/{id}/ban', name: 'ban_benevole', methods: ['POST'])]
    public function banBenevole(Benevoles $benevole): JsonResponse
    {
        $this->entityManager->remove($benevole);
        $this->entityManager->flush();
        
        return new JsonResponse(['status' => 'Benevole banned']);
    }
}
