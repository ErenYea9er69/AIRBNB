<?php

namespace App\Controller;

use App\Repository\PropertyRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PropertyRepository $propertyRepository, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $categoryId = $request->query->get('category');
        $activeCategory = $categoryId ? $categoryRepository->find($categoryId) : null;

        $latestProperties = $propertyRepository->findBy([], ['id' => 'DESC'], 5);
        
        $criteria = $activeCategory ? ['category' => $activeCategory] : [];
        $recommendations = $propertyRepository->findBy($criteria, ['rating' => 'DESC'], 10);
        
        $categories = $categoryRepository->findAll();
        $agents = $entityManager->getRepository(\App\Entity\Agent::class)->findBy([], ['id' => 'DESC'], 5);

        return $this->render('main/index.html.twig', [
            'latestProperties' => $latestProperties,
            'recommendations' => $recommendations,
            'categories' => $categories,
            'activeCategoryId' => $categoryId,
            'agents' => $agents,
        ]);
    }

    #[Route('/explore', name: 'app_explore')]
    public function explore(PropertyRepository $propertyRepository, Request $request): Response
    {
        $query = $request->query->get('query');
        $filter = $request->query->get('filter', 'All'); // e.g., 'All', 'sale', 'rent'

        $qb = $propertyRepository->createQueryBuilder('p');

        if ($query) {
            $qb->andWhere('p.title LIKE :query OR p.address LIKE :query')
               ->setParameter('query', '%' . $query . '%');
        }

        if ($filter && $filter !== 'All') {
            $qb->andWhere('p.listingType = :filter')
               ->setParameter('filter', $filter);
        }

        $properties = $qb->getQuery()->getResult();

        return $this->render('main/explore.html.twig', [
            'properties' => $properties,
        ]);
    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('main/profile.html.twig', [
            'savedProperties' => $user->getSavedProperties(),
            'bookings' => $user->getBookings(),
        ]);
    }

    #[Route('/profile/settings', name: 'app_profile_settings', methods: ['GET', 'POST'])]
    public function settings(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $success = null;
        $error = null;

        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');

            if ($action === 'update_profile') {
                $name = $request->request->get('name');
                $email = $request->request->get('email');
                if ($name) { $user->setName($name); }
                if ($email) { $user->setEmail($email); }
                $entityManager->flush();
                $success = 'Profile updated successfully.';
            }

            if ($action === 'change_password') {
                $currentPassword = $request->request->get('current_password');
                $newPassword = $request->request->get('new_password');
                $confirmPassword = $request->request->get('confirm_password');

                if (!$userPasswordHasher->isPasswordValid($user, $currentPassword)) {
                    $error = 'Current password is incorrect.';
                } elseif ($newPassword !== $confirmPassword) {
                    $error = 'New passwords do not match.';
                } elseif (strlen($newPassword) < 8) {
                    $error = 'Password must be at least 8 characters.';
                } else {
                    $user->setPassword($userPasswordHasher->hashPassword($user, $newPassword));
                    $entityManager->flush();
                    $success = 'Password changed successfully.';
                }
            }
        }

        return $this->render('main/settings.html.twig', [
            'success' => $success,
            'error' => $error,
        ]);
    }
}
