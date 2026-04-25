<?php

namespace App\Controller;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/seller')]
#[IsGranted('ROLE_SELLER')]
class SellerController extends AbstractController
{
    #[Route('/dashboard', name: 'app_seller_dashboard')]
    public function index(PropertyRepository $propertyRepository): Response
    {
        $user = $this->getUser();
        $agent = $user->getAgent();

        $properties = [];
        if ($agent) {
            $properties = $propertyRepository->findBy(['agent' => $agent]);
        }

        return $this->render('seller/dashboard.html.twig', [
            'properties' => $properties,
            'agent' => $agent,
        ]);
    }

    #[Route('/property/new', name: 'app_seller_property_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $agent = $this->getUser()->getAgent();
        if (!$agent) {
            // Failsafe: only agents can create properties
            return $this->redirectToRoute('app_seller_dashboard');
        }

        $property = new Property();
        // Set default rating to 0 when creating a new property
        $property->setRating(0);
        
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $property->setAgent($agent);
            
            $entityManager->persist($property);
            $entityManager->flush();

            return $this->redirectToRoute('app_seller_dashboard');
        }

        return $this->render('seller/new.html.twig', [
            'property' => $property,
            'form' => $form,
        ]);
    }
}
