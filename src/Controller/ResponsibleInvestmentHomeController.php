<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResponsibleInvestmentHomeController extends AbstractController
{
    #[Route('/responsible/investment/home', name: 'app_responsible_investment_home')]
    public function index(): Response
    {
        return $this->render('responsible_investment_home/index.html.twig', [
            'controller_name' => 'ResponsibleInvestmentHomeController',
        ]);
    }
}
