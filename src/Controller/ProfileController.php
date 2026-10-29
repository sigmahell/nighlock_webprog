<?php

namespace App\Controller;

use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    #[IsGranted('ROLE_USER')]
    public function index(BookingRepository $bookingRepository): Response
    {
        // --- REPOSITORY: Fetch all bookings for the logged-in user ---
        $bookings = $bookingRepository->findByClient($this->getUser());

        return $this->render('profile/index.html.twig', [
            'bookings' => $bookings,
        ]);
    }
}
