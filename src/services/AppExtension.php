<?php
namespace App\services;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ClientRepository;

class AppExtension extends AbstractExtension
{
    private $session;
    private $clientRepository;

    public function __construct(SessionInterface $session, ClientRepository $clientRepository)
    {
        $this->session = $session;
        $this->clientRepository = $clientRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_client', [$this, 'getClient']),
        ];
    }

    public function getClient()
    {
        $username = $this->session->get('userEmail');
        return $this->clientRepository->findOneBy(['email' => $username]);
    }
}
