<?php

namespace App\Controller;

use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
