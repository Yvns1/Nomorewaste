<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use App\Repository\UtilisateursRepository;
use App\Entity\Benevoles;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class InscriptionController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher, 
        LoggerInterface $logger
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
    
        // Vérifier les champs obligatoires
        if (empty($data['nom']) || empty($data['email']) || empty($data['mot_de_passe']) || empty($data['type_utilisateur'])) {
            return new JsonResponse(['success' => false, 'message' => 'Veuillez remplir tous les champs.'], Response::HTTP_BAD_REQUEST);
        }
    
        // Vérifier si l'utilisateur existe déjà
        $existingUser = $entityManager->getRepository(Utilisateurs::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['success' => false, 'message' => 'Cet email est déjà utilisé.'], Response::HTTP_BAD_REQUEST);
        }
    
        // Créer un nouvel utilisateur
        $user = new Utilisateurs();
        $user->setName($data['nom']);
        $user->setEmail($data['email']);
        $user->setUserType($data['type_utilisateur']);
    
        // Hacher le mot de passe
        try {
            $hashedPassword = $passwordHasher->hashPassword($user, $data['mot_de_passe']);
            $user->setPassword($hashedPassword);
        } catch (\Exception $e) {
            $logger->error('Erreur lors du hachage du mot de passe : ' . $e->getMessage());
            return new JsonResponse(['success' => false, 'message' => 'Erreur lors de l\'inscription.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    
        // Définir les champs supplémentaires
        $user->setCreatedAt(new \DateTimeImmutable());
        if (isset($data['informations_supplementaires'])) {
            $user->setAdditionalInfo($data['informations_supplementaires']);
        }
    
        // Sauvegarder l'utilisateur dans la base de données
        $entityManager->persist($user);
        $entityManager->flush();

         // Si l'utilisateur est un bénévole, créer un enregistrement dans la table benevoles
         if ($user->getUserType() === 'benevole') {
            $benevole = new Benevoles();
            $benevole->setSkills('N/A'); // Valeur par défaut, à ajuster si nécessaire
            $benevole->setUser($user);
            $benevole->setEmail($user->getEmail());
            $benevole->setNom($user->getName());
            $benevole->setMotDePasse($user->getPassword()); // Vous pouvez vouloir gérer cela différemment
            $benevole->setPhotoDeProfil('N/A'); // Valeur par défaut, à ajuster si nécessaire

            // Persister l'enregistrement du bénévole
            $entityManager->persist($benevole);
            $entityManager->flush();
        }
    
        return new JsonResponse(['success' => true, 'message' => 'Inscription réussie !'], Response::HTTP_CREATED);
    }
    

    private function isPasswordValid(string $password): bool
    {
        return strlen($password) >= 8 &&
            preg_match('/[A-Z]/', $password) &&
            preg_match('/[0-9]/', $password) &&
            preg_match('/[\W]/', $password); // \W correspond à n'importe quel caractère non alphanumérique
    }
}
