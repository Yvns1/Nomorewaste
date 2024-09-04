<?php

namespace App\Controller\Api;

use App\Repository\UtilisateursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class VolunteerApiController extends AbstractController
{
    #[Route('/api/volunteers', name: 'api_volunteer_list', methods: ['GET'])]
    public function list(UtilisateursRepository $utilisateursRepository): JsonResponse
    {
        $volunteers = $utilisateursRepository->findBy(['userType' => 'bénévole']);

        $volunteerData = [];
        foreach ($volunteers as $volunteer) {
            $volunteerData[] = [
                'id' => $volunteer->getId(),
                'name' => $volunteer->getName(),
            ];
        }

        return new JsonResponse($volunteerData);
    }
}
