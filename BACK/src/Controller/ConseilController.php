<?php

namespace App\Controller;

use App\Entity\Conseil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConseilController extends AbstractController
{
    #[Route('/api/conseils', name: 'create_conseil', methods: ['POST'])]
    public function createConseil(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);

        $conseil = new Conseil();
        $conseil->setTypeConseil($data['typeConseil']);
        $conseil->setTitre($data['titre']);
        $conseil->setAuteur($data['auteur']);
        $conseil->setDate(new \DateTime($data['date']));
        $conseil->setTexte($data['texte']);

        $em->persist($conseil);
        $em->flush();

        return new Response('Conseil créé avec succès', Response::HTTP_CREATED);
    }


    #[Route('/api/list-conseils', name: 'list_conseils', methods: ['GET'])]
    public function listConseils(EntityManagerInterface $em, SerializerInterface $serializer): Response
    {
        $conseils = $em->getRepository(Conseil::class)->findAll();
    
        $jsonContent = $serializer->serialize($conseils, 'json');
    
        return new Response($jsonContent, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

   

    

    #[Route('/api/conseils/{id}', name: 'delete_conseil', methods: ['DELETE'])]
    public function deleteConseil($id, EntityManagerInterface $em): Response
    {
        $conseil = $em->getRepository(Conseil::class)->find($id);

        if (!$conseil) {
            return new Response('Conseil non trouvé', Response::HTTP_NOT_FOUND);
        }

        $em->remove($conseil);
        $em->flush();

        return new Response('Conseil supprimé avec succès', Response::HTTP_OK);
    }

    #[Route('/api/conseils/{id}', name: 'edit_conseil', methods: ['PUT'])]
    public function editConseil($id, Request $request, EntityManagerInterface $em): Response
    {
        $conseil = $em->getRepository(Conseil::class)->find($id);
    
        if (!$conseil) {
            return new Response('Conseil non trouvé', Response::HTTP_NOT_FOUND);
        }
    
        $data = json_decode($request->getContent(), true);
    
        // Mise à jour des champs seulement s'ils sont présents dans la requête
        if (isset($data['typeConseil'])) {
            $conseil->setTypeConseil($data['typeConseil']);
        }
        if (isset($data['titre'])) {
            $conseil->setTitre($data['titre']);
        }
        if (isset($data['auteur'])) {
            $conseil->setAuteur($data['auteur']);
        }
        if (isset($data['date'])) {
            $conseil->setDate(new \DateTime($data['date']));
        }
        if (isset($data['texte'])) {
            $conseil->setTexte($data['texte']);
        }
    
        $em->flush();
    
        return new Response('Conseil mis à jour avec succès', Response::HTTP_OK);
    }
    
}
