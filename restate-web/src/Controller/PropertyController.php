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

        $similarProperties = $propertyRepository->findBy(
            ['category' => $property->getCategory()],
            ['id' => 'DESC'],
            4
        );
        // Filter out current property
        $similarProperties = array_filter($similarProperties, fn($p) => $p->getId() !== $property->getId());

        return $this->render('property/show.html.twig', [
            'property' => $property,
            'similarProperties' => $similarProperties,
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

        $visitDate = $request->request->get('visit_date');
        $message = $request->request->get('message');

        if (!$visitDate) {
            $this->addFlash('error', 'Please select a visit date.');
            return $this->redirectToRoute('app_property_show', ['id' => $id]);
        }

        $booking = new Booking();
        $booking->setProperty($property);
        $booking->setUser($this->getUser());
        $booking->setVisitDate(new \DateTime($visitDate));
        $booking->setMessage($message);
        $booking->setStatus('pending');

        $entityManager->persist($booking);
        $entityManager->flush();

        $this->addFlash('success', 'Your visit request has been sent to the agent!');

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

        // Update property average rating
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
