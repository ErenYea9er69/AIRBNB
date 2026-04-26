<?php

namespace App\Command;

use App\Entity\Agent;
use App\Entity\Category;
use App\Entity\Property;
use App\Entity\Review;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:seed',
    description: 'Seed the database with real-looking data',
)]
class AppSeedCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // 1. Create Categories
        $categories = [];
        $categoryNames = ['House', 'Villa', 'Apartment', 'Office', 'Penthouse', 'Cottage'];
        foreach ($categoryNames as $name) {
            $existing = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => $name]);
            if ($existing) {
                $category = $existing;
            } else {
                $category = new Category();
                $category->setName($name);
                $this->entityManager->persist($category);
            }
            $categories[] = $category;
        }

        // 2. Create Features
        $features = [];
        $featureData = [
            ['name' => 'WiFi', 'icon' => 'icons/wifi.png'],
            ['name' => 'Swimming Pool', 'icon' => 'icons/swim.png'],
            ['name' => 'Parking', 'icon' => 'icons/car-park.png'],
            ['name' => 'Gym', 'icon' => 'icons/dumbell.png'],
            ['name' => 'Laundry', 'icon' => 'icons/laundry.png'],
            ['name' => 'Pet Friendly', 'icon' => 'icons/dog.png'],
        ];
        foreach ($featureData as $fd) {
            $existing = $this->entityManager->getRepository(\App\Entity\Feature::class)->findOneBy(['name' => $fd['name']]);
            if ($existing) {
                $feature = $existing;
            } else {
                $feature = new \App\Entity\Feature();
                $feature->setName($fd['name']);
                $feature->setIcon($fd['icon']);
                $this->entityManager->persist($feature);
            }
            $features[] = $feature;
        }

        // 3. Create Users & Agents
        $users = [];
        $agents = [];
        
        $sellerData = [
            ['name' => 'Rayen Benaissa', 'email' => 'rayen@restate.com', 'avatar' => 'images/avatar.png', 'bio' => 'Top-rated agent with 10+ years experience in luxury villas.', 'phone' => '+1 234 567 890'],
            ['name' => 'Natasya Putri', 'email' => 'natasya@restate.com', 'avatar' => 'images/avatar.png', 'bio' => 'Specializing in modern apartments and urban living spaces.', 'phone' => '+1 987 654 321'],
            ['name' => 'Laury Smith', 'email' => 'laury@restate.com', 'avatar' => 'images/avatar.png', 'bio' => 'Eco-friendly homes expert and architectural consultant.', 'phone' => '+1 555 000 111'],
        ];

        foreach ($sellerData as $data) {
            $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
            if ($existingUser) {
                $user = $existingUser;
            } else {
                $user = new User();
                $user->setEmail($data['email']);
                $user->setName($data['name']);
                $user->setUserType('seller');
                $user->setRoles(['ROLE_SELLER']);
                $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
                $this->entityManager->persist($user);
            }
            $users[] = $user;

            $existingAgent = $this->entityManager->getRepository(Agent::class)->findOneBy(['email' => $data['email']]);
            if ($existingAgent) {
                $agent = $existingAgent;
            } else {
                $agent = new Agent();
                $agent->setName($data['name']);
                $agent->setEmail($data['email']);
                $agent->setAvatar($data['avatar']);
                $agent->setBio($data['bio']);
                $agent->setPhone($data['phone']);
                $agent->setUser($user);
                $this->entityManager->persist($agent);
            }
            $agents[] = $agent;
        }

        // 3. Create Properties
        $propertyData = [
            ['title' => 'Modern Villa with Ocean View', 'price' => 1250000, 'address' => 'Malibu Beach, CA', 'city' => 'Malibu', 'state' => 'California', 'lat' => 34.0259, 'lng' => -118.7798],
            ['title' => 'Luxury Penthouse in Downtown', 'price' => 850000, 'address' => '7th Avenue, NY', 'city' => 'New York', 'state' => 'New York', 'lat' => 40.7128, 'lng' => -74.0060],
            ['title' => 'Cozy Mountain Retreat', 'price' => 450000, 'address' => 'Alpine Road, CO', 'city' => 'Aspen', 'state' => 'Colorado', 'lat' => 39.1911, 'lng' => -106.8175],
            ['title' => 'Minimalist Urban Apartment', 'price' => 320000, 'address' => 'Skyline Dr, IL', 'city' => 'Chicago', 'state' => 'Illinois', 'lat' => 41.8781, 'lng' => -87.6298],
            ['title' => 'Sustainable Eco-House', 'price' => 680000, 'address' => 'Green Valley, OR', 'city' => 'Portland', 'state' => 'Oregon', 'lat' => 45.5152, 'lng' => -122.6784],
            ['title' => 'Classic Heritage Mansion', 'price' => 2100000, 'address' => 'Old Oak Rd, MA', 'city' => 'Boston', 'state' => 'Massachusetts', 'lat' => 42.3601, 'lng' => -71.0589],
        ];

        foreach ($propertyData as $i => $p) {
            $property = new Property();
            $property->setTitle($p['title']);
            $property->setPrice($p['price']);
            $property->setAddress($p['address']);
            $property->setCity($p['city']);
            $property->setState($p['state']);
            $property->setLatitude($p['lat']);
            $property->setLongitude($p['lng']);
            $property->setArea(rand(1200, 5000));
            $property->setBedrooms(rand(2, 6));
            $property->setBathrooms(rand(1, 4));
            $property->setListingType($i % 2 == 0 ? 'sale' : 'rent');
            $property->setStatus('available');
            $property->setRating(4.5 + (rand(0, 5) / 10));
            $property->setImage('images/japan.png'); // Placeholder path that exists in project
            $property->setCategory($categories[array_rand($categories)]);
            $property->setAgent($agents[array_rand($agents)]);
            $property->setDescription('Experience luxury at its finest in this stunning property. Featuring modern amenities, spacious rooms, and breathtaking views, this home is perfect for those seeking comfort and style.');
            
            // Add some features
            $randFeatures = (array) array_rand($features, 3);
            foreach ($randFeatures as $fIdx) {
                $property->addFeature($features[$fIdx]);
            }

            $this->entityManager->persist($property);

            // Add some reviews
            for ($j = 0; $j < 3; $j++) {
                $review = new Review();
                $review->setRating(rand(4, 5));
                $review->setReview("Amazing property! The attention to detail is incredible and the location is perfect. Highly recommended.");
                $review->setProperty($property);
                $review->setUser($users[array_rand($users)]);
                $this->entityManager->persist($review);
            }
        }

        $this->entityManager->flush();

        $io->success('Database seeded successfully with Categories, Agents, Properties, and Reviews!');

        return Command::SUCCESS;
    }
}
