<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Property;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PropertyController extends AbstractController
{
    #[Route('/properties/{id}', name: 'app_property_show')]
    public function show(int $id, PropertyRepository $propertyRepository): Response
    {
        $property = $propertyRepository->find($id);

        if (!$property) {
            throw $this->createNotFoundException('Property not found');
        }

        return $this->render('property/show.html.twig', [
            'property' => $property,
        ]);
    }

    #[Route('/properties/{id}/book', name: 'app_property_book', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function book(int $id, Request $request, PropertyRepository $propertyRepository, EntityManagerInterface $entityManager): Response
    {
        $property = $propertyRepository->find($id);
        if (!$property) {
            throw $this->createNotFoundException('Property not found');
        }

        $startDate = $request->request->get('start_date');
        $endDate = $request->request->get('end_date');
        $message = $request->request->get('message');

        if (!$startDate || !$endDate) {
            $this->addFlash('error', 'Please select both arrival and departure dates.');
            return $this->redirectToRoute('app_property_show', ['id' => $id]);
        }

        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $now = new \DateTime('today');

        if ($start < $now) {
            $this->addFlash('error', 'The arrival date cannot be in the past.');
            return $this->redirectToRoute('app_property_show', ['id' => $id]);
        }

        if ($end < $start) {
            $this->addFlash('error', 'The departure date cannot be before the arrival date.');
            return $this->redirectToRoute('app_property_show', ['id' => $id]);
        }

        $booking = new Booking();
        $booking->setProperty($property);
        $booking->setUser($this->getUser());
        $booking->setVisitDate($start);
        $booking->setEndDate($end);
        $booking->setMessage($message);
        $booking->setStatus('pending');

        $property->setStatus('not available');

        $entityManager->persist($booking);
        $entityManager->flush();

        $this->addFlash('success', 'Your reservation request has been sent to the agent!');

        return $this->redirectToRoute('app_property_show', ['id' => $id]);
    }

    #[Route('/properties/{id}/review', name: 'app_property_review', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function review(int $id, Request $request, PropertyRepository $propertyRepository, EntityManagerInterface $entityManager): Response
    {
        $property = $propertyRepository->find($id);
        if (!$property) {
            throw $this->createNotFoundException('Property not found');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if ($user->getAgent() !== null && $property->getAgent() === $user->getAgent()) {
            $this->addFlash('error', 'You cannot review your own property.');
            return $this->redirectToRoute('app_property_show', ['id' => $id]);
        }

        $rating = $request->request->get('rating');
        $comment = $request->request->get('review');

        if (!$rating || !$comment) {
            $this->addFlash('error', 'Please provide both a rating and a comment.');
            return $this->redirectToRoute('app_property_show', ['id' => $id]);
        }

        $review = new \App\Entity\Review();
        $review->setProperty($property);
        $review->setUser($this->getUser());
        $review->setRating((float)$rating);
        $review->setReview($comment);

        $entityManager->persist($review);

        $reviews = $property->getReviews();
        $totalRating = 0;
        foreach ($reviews as $r) {
            $totalRating += $r->getRating();
        }
        $totalRating += (float)$rating;
        $property->setRating(round($totalRating / ($reviews->count() + 1), 1));

        $entityManager->flush();

        $this->addFlash('success', 'Thank you for your review!');

        return $this->redirectToRoute('app_property_show', ['id' => $id]);
    }
}
