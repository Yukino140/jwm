<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResponsibleLoanHomeController extends AbstractController
{
    #[Route('/responsible/loan/home', name: 'app_responsible_loan_home')]
    public function index(): Response
    {
        return $this->render('responsible_loan_home/index.html.twig', [
            'controller_name' => 'ResponsibleLoanHomeController',
        ]);
    }
}
