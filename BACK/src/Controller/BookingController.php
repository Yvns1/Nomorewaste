<?php

// src/Controller/BookingController.php

namespace App\Controller;

use App\Entity\Utilisateurs;
use App\Entity\Booking;
use App\Entity\CookingClass;
use App\Repository\BookingRepository;
use App\Repository\CookingClassRepository;
use App\Repository\UtilisateursRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/booking')]
class BookingController extends AbstractController
{
    #[Route('/', name: 'booking_index', methods: ['GET'])]
    public function index(BookingRepository $bookingRepository): Response
    {
        return $this->render('booking/index.html.twig', [
            'bookings' => $bookingRepository->findBy(['user' => $this->getUser()]),
        ]);
    }

    #[Route('/new', name: 'api_booking_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CookingClassRepository $cookingClassRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Récupérer l'utilisateur et le cours de cuisine
        $user = $entityManager->getRepository(Utilisateurs::class)->find($data['userId']);
        $cookingClass = $cookingClassRepository->find($data['cookingClassId']);

        if (!$user || !$cookingClass) {
            return new JsonResponse(['message' => 'Invalid data.'], Response::HTTP_BAD_REQUEST);
        }

        if ($cookingClass->getAvailableSlots() <= 0) {
            return new JsonResponse(['message' => 'No available slots.'], Response::HTTP_BAD_REQUEST);
        }

        // Créer la réservation
        $booking = new Booking();
        $booking->setUser($user);
        $booking->setCookingClass($cookingClass);
        $booking->setUserName($user->getName());
        $booking->setUserEmail($user->getEmail());
        $booking->setUserType($user->getUserType());

        // Incrémenter le nombre de réservations pour le cours de cuisine
        $cookingClass->setMaxParticipants($cookingClass->getMaxParticipants() - 1);

        $entityManager->persist($booking);
        $entityManager->persist($cookingClass); // Nécessaire pour sauvegarder la décrémentation
        $entityManager->flush();

        return new JsonResponse(['message' => 'Booking confirmed.'], Response::HTTP_CREATED);
    }

    #[Route('/by-cooking-class/{id}', name: 'api_bookings_by_cooking_class', methods: ['GET'])]
    public function getBookingsByCookingClass($id, BookingRepository $bookingRepository): JsonResponse
    {
        $cookingClassId = (int) $id; // Forcer la conversion en entier

        $bookings = $bookingRepository->findBy(['cookingClass' => $cookingClassId]);

        if (!$bookings) {
            return new JsonResponse(['message' => 'No bookings found for this cooking class.'], Response::HTTP_NOT_FOUND);
        }

        $bookingData = [];
        foreach ($bookings as $booking) {
            $bookingData[] = [
                'id' => $booking->getId(),
                'userName' => $booking->getUserName(),
                'userEmail' => $booking->getUserEmail(),
                'userType' => $booking->getUserType(),
                'bookedAt' => $booking->getBookedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($bookingData, Response::HTTP_OK);
    }

    #[Route('/check/{cookingClassId}/{userId}', name: 'api_booking_check', methods: ['GET'])]
    public function checkUserBooking($cookingClassId, $userId, BookingRepository $bookingRepository): JsonResponse
    {
        $booking = $bookingRepository->findOneBy([
            'cookingClass' => $cookingClassId,
            'user' => $userId,
        ]);

        return new JsonResponse(['isReserved' => $booking !== null], Response::HTTP_OK);
    }

    #[Route('/cancel', name: 'api_booking_cancel', methods: ['POST'])]
    public function cancelBooking(Request $request, EntityManagerInterface $entityManager, BookingRepository $bookingRepository, CookingClassRepository $cookingClassRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Récupérer la réservation
        $booking = $bookingRepository->findOneBy([
            'cookingClass' => $data['cookingClassId'],
            'user' => $data['userId'],
        ]);

        if (!$booking) {
            return new JsonResponse(['message' => 'Booking not found.'], Response::HTTP_NOT_FOUND);
        }

        // Récupérer le cours de cuisine
        $cookingClass = $cookingClassRepository->find($data['cookingClassId']);
        if (!$cookingClass) {
            return new JsonResponse(['message' => 'Cooking class not found.'], Response::HTTP_NOT_FOUND);
        }

        // Supprimer la réservation
        $entityManager->remove($booking);

        // Décrémenter le nombre de réservations pour le cours de cuisine
        $cookingClass->setMaxParticipants($cookingClass->getMaxParticipants() + 1);

        $entityManager->persist($cookingClass); // Nécessaire pour sauvegarder la décrémentation
        $entityManager->flush();

        return new JsonResponse(['message' => 'Booking cancelled.'], Response::HTTP_OK);
    }

    #[Route('/user-reservations/{userId}', name: 'api_user_reservations', methods: ['GET'])]
    public function getUserReservations($userId, BookingRepository $bookingRepository): JsonResponse
    {
        $bookings = $bookingRepository->findBy(['user' => $userId]);

        if (!$bookings) {
            return new JsonResponse(['message' => 'No bookings found for this user.'], Response::HTTP_NOT_FOUND);
        }

        $bookingData = [];
        foreach ($bookings as $booking) {
            $bookingData[] = [
                'id' => $booking->getId(),
                'cookingClassId' => $booking->getCookingClass()->getId(),
                'cookingClassTitle' => $booking->getCookingClass()->getTitle(),
                'bookedAt' => $booking->getBookedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($bookingData, Response::HTTP_OK);
    }
}
