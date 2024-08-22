<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        $dashboardUrl = $this->generateUrl('admin_dashboard');

        return new Response(
            '<h1>Bienvenue sur la page d\'accueil !</h1>' .
            '<a href="' . $dashboardUrl . '" style="display: inline-block; margin-top: 20px; padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">Accéder au Dashboard</a>'
        );
    }

    #[Route('/about', name: 'about_page')]
    public function about(): Response
    {
        return new Response('Page à propos');
    }
}
