<?php

namespace App\Controller;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/seller')]
#[IsGranted('ROLE_SELLER')]
class SellerController extends AbstractController
{
    #[Route('/dashboard', name: 'app_seller_dashboard')]
    public function index(PropertyRepository $propertyRepository): Response
    {
        /** @var \App\Entity\User $user */
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
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $agent = $user->getAgent();
        if (!$agent) {
            return $this->redirectToRoute('app_seller_dashboard');
        }

        $property = new Property();
        $property->setRating(0);
        
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/properties',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // handle exception
                }
                $property->setImage('uploads/properties/'.$newFilename);
            } else {
                $property->setImage('images/japan.png'); // fallback
            }

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

    #[Route('/property/{id}/edit', name: 'app_seller_property_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Property $property, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $agent = $user->getAgent();
        if ($property->getAgent() !== $agent) {
            return $this->redirectToRoute('app_seller_dashboard');
        }

        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/properties',
                        $newFilename
                    );
                    $property->setImage('uploads/properties/'.$newFilename);
                } catch (FileException $e) {
                    // handle exception
                }
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_seller_dashboard');
        }

        return $this->render('seller/edit.html.twig', [
            'property' => $property,
            'form' => $form,
        ]);
    }

    #[Route('/property/{id}/delete', name: 'app_seller_property_delete', methods: ['POST'])]
    public function delete(Request $request, Property $property, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $agent = $user->getAgent();
        if ($property->getAgent() === $agent) {
            if ($this->isCsrfTokenValid('delete'.$property->getId(), $request->request->get('_token'))) {
                $entityManager->remove($property);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('app_seller_dashboard');
    }
}
