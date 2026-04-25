<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Repository\AgentRepository;
use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AgentController extends AbstractController
{
    #[Route('/agent/{id}', name: 'app_agent_show')]
    public function show(Agent $agent, PropertyRepository $propertyRepository): Response
    {
        $properties = $propertyRepository->findBy(['agent' => $agent], ['id' => 'DESC']);

        return $this->render('agent/show.html.twig', [
            'agent' => $agent,
            'properties' => $properties,
        ]);
    }
}
