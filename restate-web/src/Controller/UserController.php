<?php

namespace App\Controller;

use App\Entity\Property;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
#[IsGranted('ROLE_USER')]
class UserController extends AbstractController
{
    #[Route('/favorite/{id}', name: 'app_user_favorite', methods: ['POST'])]
    public function toggleFavorite(Property $property, EntityManagerInterface $em): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        $isFavorite = false;
        if ($user->getSavedProperties()->contains($property)) {
            $user->removeSavedProperty($property);
        } else {
            $user->addSavedProperty($property);
            $isFavorite = true;
        }

        $em->flush();

        return new JsonResponse([
            'success' => true,
            'isFavorite' => $isFavorite
        ]);
    }
}
