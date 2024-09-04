<?php
namespace App\Controller;

use App\Entity\Utilisateurs;
use App\Form\UtilisateursType;
use App\Repository\UtilisateursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UtilisateursController extends AbstractController
{
    private $entityManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    // Route pour afficher la liste des utilisateurs (page web)
    #[Route('/utilisateurs', name: 'utilisateurs_index', methods: ['GET'])]
    public function index(UtilisateursRepository $utilisateursRepository): Response
    {
        $utilisateurs = $utilisateursRepository->findAll();
        return $this->render('utilisateurs/index.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }

    #[Route('/utilisateurs/{id}', name: 'get_utilisateur', methods: ['GET'])]
    public function getUtilisateur(int $id, UtilisateursRepository $utilisateursRepository): JsonResponse
    {
        $utilisateur = $utilisateursRepository->find($id);

        if (!$utilisateur) {
            return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'id' => $utilisateur->getId(),
            'email' => $utilisateur->getEmail(),
            // Ajoutez d'autres champs utilisateur ici
        ]);
    }

    #[Route('/api/utilisateur/{id}/edit', name: 'edit_user', methods: ['POST'])]
    public function editUser(int $id, Request $request, UtilisateursRepository $utilisateursRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $utilisateur = $utilisateursRepository->find($id);

        if (!$utilisateur) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        if (isset($data['name'])) {
            $utilisateur->setName($data['name']);
        }

        if (isset($data['email'])) {
            $utilisateur->setEmail($data['email']);
        }

        // Vérification de l'ancien mot de passe
        if (isset($data['oldPassword'], $data['newPassword'])) {
            if (!$this->passwordHasher->isPasswordValid($utilisateur, $data['oldPassword'])) {
                return new JsonResponse(['error' => 'Ancien mot de passe incorrect'], Response::HTTP_BAD_REQUEST);
            }

            // Mise à jour du mot de passe
            $utilisateur->setPassword(
                $this->passwordHasher->hashPassword($utilisateur, $data['newPassword'])
            );
        }

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Utilisateur mis à jour avec succès']);
    }

    // Route pour afficher le formulaire de création d'un utilisateur (page web)
    #[Route('/utilisateur/new', name: 'utilisateur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = new Utilisateurs();
        $form = $this->createForm(UtilisateursType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return $this->redirectToRoute('utilisateurs_index');
        }

        return $this->render('utilisateurs/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    // Route pour afficher le formulaire d'édition d'un utilisateur (page web)
    #[Route('/utilisateur/{id}/edit', name: 'utilisateur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateurs $utilisateur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UtilisateursType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('utilisateurs_index');
        }

        return $this->render('utilisateurs/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);     
    }

    #[Route('/api/commercants', name: 'get_commercants', methods: ['GET'])]
    public function getCommercants(EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupération des utilisateurs de type "commercant"
        $commercants = $entityManager->getRepository(Utilisateurs::class)->findBy(['userType' => 'commercant']);

        // Transformation des données en un format JSON
        $data = [];
        foreach ($commercants as $commercant) {
            $data[] = [
                'id' => $commercant->getId(),
                'name' => $commercant->getName(), // Supposons que votre entité Utilisateurs ait un champ "name"
            ];
        }

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }
}
