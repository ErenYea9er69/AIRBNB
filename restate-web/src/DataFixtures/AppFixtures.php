<?php

namespace App\DataFixtures;

use App\Entity\Agent;
use App\Entity\Category;
use App\Entity\Property;
use App\Entity\Booking;
use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 0. Users
        $usersData = [
            ['email' => 'admin@restate.com', 'name' => 'System Admin', 'roles' => ['ROLE_ADMIN'], 'type' => 'admin'],
            ['email' => 'seller@restate.com', 'name' => 'Agent Smith', 'roles' => ['ROLE_SELLER'], 'type' => 'seller'],
            ['email' => 'buyer@restate.com', 'name' => 'John Doe', 'roles' => ['ROLE_USER'], 'type' => 'buyer'],
        ];

        $buyerUser = null;
        foreach ($usersData as $uData) {
            $user = new \App\Entity\User();
            $user->setEmail($uData['email']);
            $user->setName($uData['name']);
            $user->setRoles($uData['roles']);
            $user->setUserType($uData['type']);
            $user->setPassword(
                $this->hasher->hashPassword($user, 'password')
            );
            $manager->persist($user);
            
            if ($uData['type'] === 'seller') {
                $agent = new Agent();
                $agent->setName($user->getName());
                $agent->setEmail($user->getEmail());
                $agent->setAvatar('images/avatar.png');
                $agent->setUser($user);
                $manager->persist($agent);
            }

            if ($uData['type'] === 'buyer') {
                $buyerUser = $user;
            }
        }

        // 1. Categories
        $categories = ['Premium Villa', 'Urban Loft', 'Smart Apartment', 'Coastal Estate', 'Mountain Retreat'];
        $categoryEntities = [];
        foreach ($categories as $catName) {
            $cat = new Category();
            $cat->setName($catName);
            $manager->persist($cat);
            $categoryEntities[$catName] = $cat;
        }

        // 2. Agents
        $agentsData = [
            ['name' => 'Laury', 'email' => 'laury@restate.com', 'avatar' => 'images/japan.png'],
            ['name' => 'Natasya', 'email' => 'natasya@restate.com', 'avatar' => 'images/new-york.png'],
        ];
        $agentEntities = [];
        foreach ($agentsData as $data) {
            $agent = new Agent();
            $agent->setName($data['name']);
            $agent->setEmail($data['email']);
            $agent->setAvatar($data['avatar']);
            $manager->persist($agent);
            $agentEntities[] = $agent;
        }

        // 3. Properties
        $propertiesData = [
            [
                'title' => 'Modernica Apartment',
                'address' => 'New York, USA',
                'price' => 2500,
                'area' => 120,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'rating' => 4.8,
                'image' => 'images/japan.png',
                'category' => 'Smart Apartment'
            ],
            [
                'title' => 'La Grand Mansion',
                'address' => 'Tokyo, Japan',
                'price' => 5000,
                'area' => 450,
                'bedrooms' => 5,
                'bathrooms' => 4,
                'rating' => 4.9,
                'image' => 'images/new-york.png',
                'category' => 'Premium Villa'
            ],
            [
                'title' => 'Skyline Condo',
                'address' => 'Dubai, UAE',
                'price' => 3200,
                'area' => 150,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'rating' => 4.7,
                'image' => 'images/japan.png',
                'category' => 'Urban Loft'
            ],
            [
                'title' => 'Forest Retreat',
                'address' => 'Oslo, Norway',
                'price' => 1800,
                'area' => 100,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'rating' => 4.5,
                'image' => 'images/new-york.png',
                'category' => 'Mountain Retreat'
            ],
        ];

        $properties = [];
        foreach ($propertiesData as $index => $pData) {
            $prop = new Property();
            $prop->setTitle($pData['title']);
            $prop->setAddress($pData['address']);
            $prop->setPrice($pData['price']);
            $prop->setArea($pData['area']);
            $prop->setBedrooms($pData['bedrooms']);
            $prop->setBathrooms($pData['bathrooms']);
            $prop->setRating($pData['rating']);
            $prop->setImage($pData['image']);
            $prop->setAgent($agentEntities[$index % count($agentEntities)]);
            $prop->setCategory($categoryEntities[$pData['category']]);
            $prop->setDescription('This is a premium property offering luxury and comfort in a prime location.');
            $manager->persist($prop);
            $properties[] = $prop;
        }

        // 4. Bookings
        foreach ($properties as $index => $prop) {
            $booking = new Booking();
            $booking->setProperty($prop);
            $booking->setUser($buyerUser);
            $booking->setVisitDate(new \DateTime('+' . ($index + 1) . ' days'));
            $booking->setMessage('I would like to visit this property.');
            $booking->setStatus($index % 2 == 0 ? 'confirmed' : 'pending');
            $manager->persist($booking);

            // 5. Reviews
            $review = new Review();
            $review->setProperty($prop);
            $review->setUser($buyerUser);
            $review->setRating(4.5 + ($index % 5) / 10);
            $review->setReview('Amazing property, the views are stunning and the neighborhood is very quiet.');
            $manager->persist($review);
        }

        $manager->flush();
    }
}
