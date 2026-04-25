<?php

namespace App\Controller;

use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
