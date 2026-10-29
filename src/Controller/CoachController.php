<?php

namespace App\Controller;

use App\Entity\Coach;
use App\Entity\Notification;
use App\Form\CoachApplicationType;
use App\Repository\CoachRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CoachController extends AbstractController
{
    #[Route('/coaches', name: 'app_coaches', methods: ['GET', 'POST'])]
    public function index(
        CoachRepository $coachRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // --- FORM HANDLING: Coach Application ---
        $coach = new Coach();
        $form = $this->createForm(CoachApplicationType::class, $coach);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Only logged-in users can apply
            if (!$this->getUser()) {
                $this->addFlash('error', 'You must be logged in to apply as a coach.');
                return $this->redirectToRoute('app_login');
            }

            // Bind logged-in user, set tier and status
            $coach->setUser($this->getUser());
            $coach->setTier('community');
            $coach->setStatus('pending');

            $entityManager->persist($coach);

            // Notify admin (or self for demo) about new application
            $notification = new Notification();
            $notification->setRecipient($this->getUser());
            $notification->setType('application_submitted');
            $notification->setTitle('Coach Application Submitted');
            $notification->setMessage(
                sprintf('Your application as "%s" (%s) has been submitted and is pending review.', $coach->getName(), $coach->getSpecialization())
            );
            $entityManager->persist($notification);

            $entityManager->flush();

            $this->addFlash('success', 'Your coach application has been submitted! We will review it shortly.');
            return $this->redirectToRoute('app_coaches');
        }

        // --- REPOSITORY: Fetch coaches by tier ---
        return $this->render('coach/index.html.twig', [
            'featured_coaches'  => $coachRepository->findApprovedByTier('featured'),
            'pro_coaches'       => $coachRepository->findApprovedByTier('pro'),
            'community_coaches' => $coachRepository->findApprovedByTier('community'),
            'application_form'  => $form->createView(),
        ]);
    }

    #[Route('/coaches/{id}', name: 'app_coach_show', methods: ['GET'])]
    public function show(Coach $coach): Response
    {
        if ($coach->getStatus() !== 'approved') {
            throw $this->createNotFoundException('This coach profile is not available.');
        }

        return $this->render('coach/show.html.twig', [
            'coach' => $coach,
        ]);
    }
}
