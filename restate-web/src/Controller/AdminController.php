<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Property;
use App\Entity\Agent;
use App\Entity\Booking;
use App\Entity\Review;
use App\Form\PropertyType;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'app_admin_dashboard')]
    public function index(
        UserRepository $userRepository,
        PropertyRepository $propertyRepository,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $em
    ): Response {
        $stats = [
            'users' => $userRepository->count([]),
            'properties' => $propertyRepository->count([]),
            'categories' => $categoryRepository->count([]),
            'bookings' => $em->getRepository(Booking::class)->count([]),
            'revenue' => $em->getRepository(Property::class)->createQueryBuilder('p')
                ->select('SUM(p.price)')
                ->getQuery()
                ->getSingleScalarResult() ?? 0
        ];

        $recentUsers = $userRepository->findBy([], ['id' => 'DESC'], 5);

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
            'recentUsers' => $recentUsers,
        ]);
    }

    #[Route('/users', name: 'app_admin_users')]
    public function users(UserRepository $userRepository): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/user/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function editUser(User $user, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        if ($request->isMethod('POST')) {
            $user->setName($request->request->get('name'));
            $user->setEmail($request->request->get('email'));
            $userType = $request->request->get('user_type');
            $user->setUserType($userType);
            
            if ($userType === 'seller' && !$user->getAgent()) {
                $agent = new Agent();
                $agent->setName($user->getName());
                $agent->setEmail($user->getEmail());
                $agent->setAvatar('images/avatar.png');
                $agent->setUser($user);
                $em->persist($agent);
            }
            
            $newPassword = $request->request->get('password');
            if ($newPassword) {
                $user->setPassword($hasher->hashPassword($user, $newPassword));
            }

            $roles = $request->request->all('roles');
            $user->setRoles($roles);

            $em->flush();
            $this->addFlash('success', 'User node updated.');
            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('admin/user_edit.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/{id}/toggle-role', name: 'app_admin_user_toggle_role', methods: ['POST'])]
    public function toggleUserRole(User $user, EntityManagerInterface $em): Response
    {
        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles)) {
            $user->setRoles(['ROLE_USER']);
        } else {
            $user->setRoles(['ROLE_ADMIN']);
        }
        $em->flush();

        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/user/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    public function deleteUser(User $user, EntityManagerInterface $em): Response
    {
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'Self-termination prohibited.');
            return $this->redirectToRoute('app_admin_users');
        }

        if ($user->getAgent()) {
            foreach ($user->getAgent()->getProperties() as $property) {
                $em->remove($property);
            }
        }

        $reviews = $em->getRepository(Review::class)->findBy(['user' => $user]);
        foreach ($reviews as $review) {
            $em->remove($review);
        }

        $em->remove($user);
        $em->flush();
        $this->addFlash('success', 'User and associated data purged.');
        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/properties', name: 'app_admin_properties')]
    public function properties(PropertyRepository $propertyRepository, CategoryRepository $categoryRepository, Request $request): Response
    {
        $categoryId = $request->query->get('category');
        $criteria = $categoryId ? ['category' => $categoryId] : [];
        
        return $this->render('admin/properties.html.twig', [
            'properties' => $propertyRepository->findBy($criteria, ['category' => 'ASC', 'id' => 'DESC']),
            'categories' => $categoryRepository->findAll(),
            'currentCategory' => $categoryId,
        ]);
    }

    #[Route('/property/{id}/edit', name: 'app_admin_property_edit', methods: ['GET', 'POST'])]
    public function editProperty(
        Property $property, 
        Request $request, 
        EntityManagerInterface $em, 
        SluggerInterface $slugger
    ): Response {
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move($this->getParameter('kernel.project_dir').'/public/uploads/properties', $newFilename);
                $property->setImage('uploads/properties/'.$newFilename);
            }

            $em->flush();
            $this->addFlash('success', 'Listing updated by Administrator.');
            return $this->redirectToRoute('app_admin_properties');
        }

        return $this->render('admin/property_edit.html.twig', [
            'property' => $property,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/property/{id}/delete', name: 'app_admin_property_delete', methods: ['POST'])]
    public function deleteProperty(Property $property, EntityManagerInterface $em): Response
    {
        $hasConfirmedBookings = false;
        foreach ($property->getBookings() as $booking) {
            if ($booking->getStatus() === 'confirmed') {
                $hasConfirmedBookings = true;
                break;
            }
        }
        
        if ($hasConfirmedBookings) {
            $this->addFlash('error', 'Cannot delete property with active confirmed bookings.');
            return $this->redirectToRoute('app_admin_properties');
        }

        $em->remove($property);
        $em->flush();
        $this->addFlash('success', 'Property removed by Administrator.');
        return $this->redirectToRoute('app_admin_properties');
    }

    #[Route('/categories', name: 'app_admin_categories', methods: ['GET', 'POST'])]
    public function categories(Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            if ($name) {
                $category = new Category();
                $category->setName($name);
                $em->persist($category);
                $em->flush();
                $this->addFlash('success', 'Category created successfully.');
            }
        }

        return $this->render('admin/categories.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/category/{id}/delete', name: 'app_admin_category_delete', methods: ['POST'])]
    public function deleteCategory(Category $category, EntityManagerInterface $em, PropertyRepository $propertyRepository): Response
    {
        $properties = $propertyRepository->findBy(['category' => $category]);
        foreach ($properties as $prop) {
            $prop->setCategory(null);
        }
        $em->remove($category);
        $em->flush();
        $this->addFlash('success', 'Category removed.');
        return $this->redirectToRoute('app_admin_categories');
    }



    #[Route('/bookings', name: 'app_admin_bookings')]
    public function bookings(EntityManagerInterface $em): Response
    {
        return $this->render('admin/bookings.html.twig', [
            'bookings' => $em->getRepository(Booking::class)->findAll(),
        ]);
    }

    #[Route('/booking/{id}/status/{status}', name: 'app_admin_booking_status', methods: ['POST'])]
    public function updateBookingStatus(Booking $booking, string $status, EntityManagerInterface $em): Response
    {
        $booking->setStatus($status);
        $em->flush();
        $this->addFlash('success', 'Interaction state updated.');
        return $this->redirectToRoute('app_admin_bookings');
    }

    #[Route('/booking/{id}/delete', name: 'app_admin_booking_delete', methods: ['POST'])]
    public function deleteBooking(Booking $booking, EntityManagerInterface $em): Response
    {
        $em->remove($booking);
        $em->flush();
        $this->addFlash('success', 'Interaction purged.');
        return $this->redirectToRoute('app_admin_bookings');
    }

    #[Route('/reviews', name: 'app_admin_reviews')]
    public function reviews(EntityManagerInterface $em): Response
    {
        return $this->render('admin/reviews.html.twig', [
            'reviews' => $em->getRepository(Review::class)->findAll(),
        ]);
    }

    #[Route('/review/{id}/delete', name: 'app_admin_review_delete', methods: ['POST'])]
    public function deleteReview(Review $review, EntityManagerInterface $em): Response
    {
        $em->remove($review);
        $em->flush();
        $this->addFlash('success', 'Review removed by Administrator.');
        return $this->redirectToRoute('app_admin_reviews');
    }
}