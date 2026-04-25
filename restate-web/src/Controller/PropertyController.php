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
}
