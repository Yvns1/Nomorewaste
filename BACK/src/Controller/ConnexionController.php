<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ConnexionController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
public function login(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, SessionInterface $session): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    // Vérifier les champs obligatoires
    if (empty($data['email']) || empty($data['mot_de_passe'])) {
        return new JsonResponse(['success' => false, 'message' => 'Veuillez remplir tous les champs.'], Response::HTTP_BAD_REQUEST);
    }

    // Vérifier si l'utilisateur existe
    $user = $entityManager->getRepository(Utilisateurs::class)->findOneBy(['email' => $data['email']]);
    if (!$user) {
        return new JsonResponse(['success' => false, 'message' => 'Cet utilisateur n\'existe pas.'], Response::HTTP_BAD_REQUEST);
    }

    // Vérifier le mot de passe
    if (!$passwordHasher->isPasswordValid($user, $data['mot_de_passe'])) {
        return new JsonResponse(['success' => false, 'message' => 'Mot de passe incorrect.'], Response::HTTP_UNAUTHORIZED);
    }

    // Stocker les informations de l'utilisateur dans la session
    $session->set('user_id', $user->getId());

    // Optionnel : Stocker certaines informations dans un cookie sécurisé
    $response = new JsonResponse([
        'success' => true, 
        'message' => 'Connexion réussie !', 
        'user' => [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'user_type' => $user->getUserType(),
            // Ajouter d'autres informations pertinentes si nécessaire
        ]
    ]);

    $cookie = new Cookie('user_info', json_encode([
        'id' => $user->getId(),
        'email' => $user->getEmail(),
        'name' => $user->getName(), // Ajout du nom à stocker dans le cookie
    ]), time() + (2 * 365 * 24 * 60 * 60), '/', null, true, true, false, 'lax');

    $response->headers->setCookie($cookie);

    return $response;
}

}
