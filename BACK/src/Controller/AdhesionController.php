<?php

namespace App\Controller;

use App\Entity\Adhesion;
use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AdhesionController extends AbstractController
{
    #[Route('/api/adhesions', name: 'api_adhesions', methods: ['GET'])]
    public function getAdhesions(EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer les utilisateurs ayant le rôle 'adhérent'
        $adherents = $entityManager->getRepository(Utilisateurs::class)->findBy(['userType' => 'adhérent']);

        $data = [];
        foreach ($adherents as $adherent) {
            $data[] = [
                'id' => $adherent->getId(),
                'name' => $adherent->getName(),
                'email' => $adherent->getEmail(),
                'userType' => $adherent->getUserType(),
                'etat' => $adherent->getEtat(),  // Assurez-vous d'ajouter ceci
                'createdAt' => $adherent->getCreatedAt() ? $adherent->getCreatedAt()->format('Y-m-d H:i:s') : null,
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/admin/adhesions/edit/{id}', name: 'admin_adhesion_edit', methods: ['POST'])]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = $entityManager->getRepository(Utilisateurs::class)->find($id);
        if (!$utilisateur) {
            return new JsonResponse(['error' => 'Adhérent non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['name'])) {
            $utilisateur->setName($data['name']);
        }

        if (isset($data['email'])) {
            $utilisateur->setEmail($data['email']);
        }

        $entityManager->flush();

        return new JsonResponse(['status' => 'Adhérent mis à jour'], Response::HTTP_OK);
    }

    #[Route('/admin/adhesions/suspend/{id}', name: 'admin_adhesion_suspend', methods: ['POST'])]
    public function suspend(int $id, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = $entityManager->getRepository(Utilisateurs::class)->find($id);
        if (!$utilisateur) {
            return new JsonResponse(['error' => 'Adhérent non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $utilisateur->setUserType('suspendu'); // Changer le type d'utilisateur à "suspendu"
        $entityManager->flush();

        return new JsonResponse(['status' => 'Adhérent suspendu'], Response::HTTP_OK);
    }

    #[Route('/admin/adhesions/ban/{id}', name: 'admin_adhesion_ban', methods: ['POST'])]
    public function ban(int $id, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = $entityManager->getRepository(Utilisateurs::class)->find($id);
        if (!$utilisateur) {
            return new JsonResponse(['error' => 'Adhérent non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $utilisateur->setUserType('banni'); // Changer le type d'utilisateur à "banni"
        $entityManager->flush();

        return new JsonResponse(['status' => 'Adhérent banni'], Response::HTTP_OK);
    }

    #[Route('/api/admin/add-adherent', name: 'admin_add_adherent', methods: ['POST'])]
    public function addAdherent(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name']) || empty($data['email'])) {
            return new JsonResponse(['error' => 'Nom et email sont requis'], Response::HTTP_BAD_REQUEST);
        }

        // Vérifier si l'utilisateur existe déjà
        $existingUser = $entityManager->getRepository(Utilisateurs::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['error' => 'Un utilisateur avec cet email existe déjà'], Response::HTTP_CONFLICT);
        }

        // Créer un nouvel adhérent
        $adherent = new Utilisateurs();
        $adherent->setName($data['name']);
        $adherent->setEmail($data['email']);
        $adherent->setUserType('adhérent');
        $adherent->setPasswordHash(password_hash('defaultpassword', PASSWORD_BCRYPT)); // Générer un mot de passe par défaut
        $adherent->setCreatedAt(new \DateTimeImmutable()); // Définir la date de création

        $entityManager->persist($adherent);
        $entityManager->flush();

        return new JsonResponse([
            'id' => $adherent->getId(),
            'name' => $adherent->getName(),
            'email' => $adherent->getEmail(),
            'userType' => $adherent->getUserType(),
            'createdAt' => $adherent->getCreatedAt()->format('Y-m-d H:i:s'),
        ], Response::HTTP_CREATED);
    }
}