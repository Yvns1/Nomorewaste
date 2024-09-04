<?php

namespace App\Controller\Admin;

use App\Entity\CookingClass;
use App\Entity\Utilisateurs;
use App\Form\CookingClassType;
use App\Repository\CookingClassRepository;
use App\Repository\UtilisateursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/cooking-class')]
class CookingClassController extends AbstractController
{
    #[Route('/', name: 'admin_cooking_class_index', methods: ['GET'])]
    public function index(CookingClassRepository $cookingClassRepository): Response
    {
        return $this->render('admin/cooking_class/index.html.twig', [
            'cooking_classes' => $cookingClassRepository->findAll(),
        ]);
    }

    
    #[Route('/{id}', name: 'admin_cooking_class_show', methods: ['GET'])]
    public function show(CookingClass $cookingClass): Response
    {
        return $this->render('admin/cooking_class/show.html.twig', [
            'cooking_class' => $cookingClass,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_cooking_class_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CookingClass $cookingClass, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CookingClassType::class, $cookingClass);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_cooking_class_index');
        }

        return $this->render('admin/cooking_class/edit.html.twig', [
            'cooking_class' => $cookingClass,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'admin_cooking_class_delete', methods: ['POST'])]
    public function delete(Request $request, CookingClass $cookingClass, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cookingClass->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cookingClass);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_cooking_class_index');
    }
}
