<?php
namespace App\Controller\Api;

use App\Entity\CookingClass;
use App\Repository\CookingClassRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Utilisateurs;

#[Route('/api')]
class CookingClassApiController extends AbstractController
{
    #[Route('/cooking-classes', name: 'api_cooking_class_list', methods: ['GET'])]
    public function list(CookingClassRepository $cookingClassRepository): JsonResponse
    {
        // Récupérer tous les cours de cuisine
        $cookingClasses = $cookingClassRepository->findAll();

        // Vérifier si des cours ont été trouvés
        if (!$cookingClasses) {
            return $this->json(['message' => 'No cooking classes found'], Response::HTTP_NOT_FOUND);
        }

        // Retourner les données au format JSON
        return $this->json($cookingClasses, Response::HTTP_OK, [], [
            'groups' => 'cooking_class:read' // Assurez-vous que votre entité CookingClass a ce groupe de sérialisation configuré
        ]);
    }

    #[Route('/create-cooking-class', name: 'api_create_cooking_class', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validation des données
        if (empty($data['title']) || empty($data['description']) || empty($data['startTime']) || empty($data['duration']) || empty($data['maxParticipants']) || empty($data['volunteerId'])) {
            return new JsonResponse(['error' => 'Invalid data provided'], 400);
        }

        // Vérification du bénévole
        $volunteer = $entityManager->getRepository(Utilisateurs::class)->find($data['volunteerId']);
        if (!$volunteer) {
            return new JsonResponse(['error' => 'Volunteer not found'], 404);
        }

        // Création du cours de cuisine
        $cookingClass = new CookingClass();
        $cookingClass->setTitle($data['title']);
        $cookingClass->setDescription($data['description']);
        $cookingClass->setStartTime(new \DateTime($data['startTime']));
        $cookingClass->setDuration($data['duration']);
        $cookingClass->setMaxParticipants($data['maxParticipants']);
        $cookingClass->setVolunteer($volunteer);
        $cookingClass->setIsValidated(false);

        // Sauvegarde en base de données
        $entityManager->persist($cookingClass);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Cooking class created successfully!'], 201);
    }

    // Route pour modifier un cours de cuisine
    #[Route('/update-cooking-class/{id}', name: 'api_update_cooking_class', methods: ['PUT'])]
    public function update(Request $request, CookingClass $cookingClass, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset ($data['title'])){
            $cookingClass->setTitle($data['title']);
        }
        if (isset ($data['description'])){
            $cookingClass->setDescription($data['description']);
        }
        if (isset ($data['startTime'])){
            $cookingClass->setStartTime(new \DateTime($data['startTime']));
        }
        if (isset ($data['duration'])){
            $cookingClass->setDuration($data['duration']);
        }
        if (isset ($data['maxParticipants'])){
            $cookingClass->setMaxParticipants($data['maxParticipants']);
        }
        if (isset ($data['volunteerId'])){
            $volunteer = $entityManager->getRepository(Utilisateurs::class)->find($data['volunteerId']);
            if ($volunteer) {
                $cookingClass->setVolunteer($volunteer);
            }
        }if (isset($data['isValidated'])) {
            $cookingClass->setIsValidated($data['isValidated']);
        }
    
        $entityManager->flush();
    
        return new JsonResponse(['message' => 'Cooking class updated successfully!']);
    }
       

    #[Route('/delete-cooking-class/{id}', name: 'api_delete_cooking_class', methods: ['DELETE'])]
    public function delete(CookingClass $cookingClass, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($cookingClass);
        $entityManager->flush();
    
        return new JsonResponse(['message' => 'Cooking class deleted successfully'], Response::HTTP_OK);
    }
    
}
