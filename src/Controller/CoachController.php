<?php

namespace App\Controller;

use App\Entity\Coach;
use App\Entity\Notification;
use App\Entity\User;
use App\Form\CoachApplicationType;
use App\Repository\CoachRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

final class CoachController extends AbstractController
{
    #[Route('/coaches', name: 'app_coaches', methods: ['GET', 'POST'])]
    public function index(CoachRepository $coachRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $existingApplication = $user instanceof User
            ? $coachRepository->findOneByUser($user)
            : null;

        // --- FORM HANDLING: Coach Application ---
        $coach = new Coach();
        $form = $this->createForm(CoachApplicationType::class, $coach);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Only logged-in users can apply
            if (!$user instanceof User) {
                $this->addFlash('error', 'You must be logged in to apply as a coach.');
                return $this->redirectToRoute('app_login');
            }

            if ($existingApplication !== null) {
                $this->addFlash('error', 'You have already submitted a coach application.');
                return $this->redirectToRoute('app_coaches');
            }

            // Format hourly rate automatically to Philippine Pesos
            $rawRate = trim((string) $coach->getHourlyRate());
            preg_match('/(\d+(?:[\.,]\d+)?)/', $rawRate, $matches);
            if (!empty($matches[1])) {
                $cleanRate = str_replace(',', '', $matches[1]);
                $coach->setHourlyRate('₱' . $cleanRate . '/hr');
            } elseif ($rawRate !== '') {
                $coach->setHourlyRate('₱' . ltrim($rawRate, '₱$') . '/hr');
            } else {
                $coach->setHourlyRate('₱500/hr');
            }

            // Handle optional profile picture upload or assign default
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile */
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = 'coach_' . uniqid() . '.' . ($imageFile->guessExtension() ?? 'jpg');
                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/images';
                $imageFile->move($uploadDir, $newFilename);
                $coach->setImage($newFilename);
            } else {
                $coach->setImage('default-avatar.png');
            }

            // Newly registered coaches have no rating
            $coach->setRating(null);

            // Bind logged-in user, set tier and status
            $coach->setUser($user);
            $coach->setTier('community');
            $coach->setStatus('pending');

            $entityManager->persist($coach);

            // Notify admin (or self for demo) about new application
            $notification = new Notification();
            $notification->setRecipient($user);
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

        // --- REPOSITORY: Fetch all approved coaches ---
        return $this->render('coach/index.html.twig', [
            'coaches'              => $coachRepository->findAllApproved(),
            'application_form'     => $form->createView(),
            'existing_application' => $existingApplication,
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

    #[Route('/coaches/{id}/edit', name: 'app_coach_edit', methods: ['GET', 'POST'])]
    public function edit(Coach $coach, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Ensure the logged‑in user owns this coach profile
        $user = $this->getUser();
        if (!$user instanceof User || $coach->getUser()->getId() !== $user->getId()) {
            $this->addFlash('error', 'You are not authorized to edit this profile.');
            return $this->redirectToRoute('app_coach_show', ['id' => $coach->getId()]);
        }

        // Build a simple edit form (reuse fields from application but without status/tier)
        $form = $this->createFormBuilder($coach)
            ->add('name')
            ->add('specialization')
            ->add('bio')
            ->add('hourlyRate')
            ->add('imageFile', FileType::class, [
                'required'    => false,
                'mapped'      => false,
                'label'       => 'Profile Picture (PNG only)',
                'constraints' => [
                    new File(
                        mimeTypes: ['image/png'],
                        mimeTypesMessage: 'Only PNG images are allowed.',
                    ),
                ],
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle profile picture upload
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile */
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = 'coach_' . uniqid() . '.png';
                $uploadDir   = $this->getParameter('kernel.project_dir') . '/public/images';
                $imageFile->move($uploadDir, $newFilename);
                $coach->setImage($newFilename);
            }

            // Ensure hourly rate stays in pesos format
            $rawRate = trim((string) $coach->getHourlyRate());
            if (preg_match('/(\d+(?:[\.,]\d+)?)/', $rawRate, $matches)) {
                $coach->setHourlyRate('₱' . str_replace(',', '', $matches[1]) . '/hr');
            }

            $entityManager->flush();
            $this->addFlash('success', 'Your profile has been updated.');
            return $this->redirectToRoute('app_coach_show', ['id' => $coach->getId()]);
        }

        return $this->render('coach/edit.html.twig', [
            'coach' => $coach,
            'edit_form' => $form->createView(),
        ]);
    }
}
