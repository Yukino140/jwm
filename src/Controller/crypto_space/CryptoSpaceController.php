<?php

namespace App\Controller\crypto_space;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CryptoSpaceController extends AbstractController
{
    #[Route('/crypto/space', name: 'app_crypto_space')]
    public function index(): Response
    {
        return $this->render('crypto_space/crypto_space.html.twig', [
            'controller_name' => 'CryptoSpaceController',
        ]);
    }

    #[Route('/crypto/space/wallet', name: 'app_crypto_space_wallet')]
    public function wallet(): Response
    {
        return $this->render('crypto_space/my_crypto_wallet/wallet.html.twig', [
            'controller_name' => 'CryptoSpaceController',
        ]);
    }

    #[Route('/crypto/space/p2p', name: 'app_crypto_space_p2p')]
    public function p2p(): Response
    {
        return $this->render('crypto_space/p2p_space/p2p.html.twig', [
            'controller_name' => 'CryptoSpaceController',
        ]);
    }


}
