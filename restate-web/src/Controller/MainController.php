<?php

namespace App\Controller;

use App\Repository\PropertyRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PropertyRepository $propertyRepository, CategoryRepository $categoryRepository, Request $request): Response
    {
        $categoryId = $request->query->get('category');
        $activeCategory = $categoryId ? $categoryRepository->find($categoryId) : null;

        $latestProperties = $propertyRepository->findBy([], ['id' => 'DESC'], 5);
        
        $criteria = $activeCategory ? ['category' => $activeCategory] : [];
        $recommendations = $propertyRepository->findBy($criteria, ['rating' => 'DESC'], 10);
        
        $categories = $categoryRepository->findAll();

        return $this->render('main/index.html.twig', [
            'latestProperties' => $latestProperties,
            'recommendations' => $recommendations,
            'categories' => $categories,
            'activeCategoryId' => $categoryId,
        ]);
    }

    #[Route('/explore', name: 'app_explore')]
    public function explore(PropertyRepository $propertyRepository, Request $request): Response
    {
        $query = $request->query->get('query');
        $filter = $request->query->get('filter', 'All');

        // Logic for filtering will be added later
        $properties = $propertyRepository->findAll();

        return $this->render('main/explore.html.twig', [
            'properties' => $properties,
        ]);
    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        return $this->render('main/profile.html.twig');
    }
}
