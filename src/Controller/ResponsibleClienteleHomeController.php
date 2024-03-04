<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResponsibleClienteleHomeController extends AbstractController
{
    #[Route('/responsible/clientele/home', name: 'app_responsible_clientele_home')]
    public function index(): Response
    {
        return $this->render('responsible_clientele_home/index.html.twig', [
            'controller_name' => 'ResponsibleClienteleHomeController',
        ]);
    }
}
