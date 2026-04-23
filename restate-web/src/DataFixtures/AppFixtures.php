<?php

namespace App\DataFixtures;

use App\Entity\Agent;
use App\Entity\Category;
use App\Entity\Property;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 1. Categories
        $categories = ['House', 'Condo', 'Townhouse', 'Villa', 'Apartment', 'Other'];
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
                'category' => 'Apartment'
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
                'category' => 'Villa'
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
                'category' => 'Condo'
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
                'category' => 'House'
            ],
        ];

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
            $prop->setCategory($categoryEntities[$pData['category']] ?? $categoryEntities['Other']);
            $prop->setDescription('This is a premium property offering luxury and comfort in a prime location.');
            $manager->persist($prop);
        }

        $manager->flush();
    }
}
