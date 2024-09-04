<?php 
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'react_routes')]
    public function index(): Response
    {
        // Renvoie simplement une réponse pour vérifier si cela fonctionne
        return new Response('Page rendue correctement');
    }

    #[Route('/test', name: 'test_route')]
public function test(): Response
{
    return new Response('Test réussi');
}

}
