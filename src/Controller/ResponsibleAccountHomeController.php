<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResponsibleAccountHomeController extends AbstractController
{
    #[Route('/responsible/account/home', name: 'app_responsible_account_home')]
    public function index(): Response
    {
        return $this->render('responsible_account_home/index.html.twig', [
            'controller_name' => 'ResponsibleAccountHomeController',
        ]);
    }
}
