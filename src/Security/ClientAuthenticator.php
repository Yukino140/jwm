<?php

namespace App\Security;

use App\Entity\Client;
use App\Entity\ResponsibleAccount;
use App\Entity\ResponsibleClientele;
use App\Entity\ResponsibleInvestment;
use App\Entity\ResponsibleLoan;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class ClientAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;
    private EntityManagerInterface $entityManager;

    public function __construct(UrlGeneratorInterface $urlGenerator, EntityManagerInterface $entityManager)
    {
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $request->getSession()->set(Security::LAST_USERNAME, $email);

        $user = $this->findUserByEmail($email);
        if (!$user) {
            // If no user found across entities, throw an exception
            throw new CustomUserMessageAuthenticationException('Login information not valid.');
        }

        return new Passport(
            new UserBadge($email, function() use ($user) { return $user; }),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    private function findUserByEmail(string $email)
    {
        $entities = [Client::class, ResponsibleAccount::class, ResponsibleLoan::class, ResponsibleClientele::class, ResponsibleInvestment::class];
        foreach ($entities as $entity) {
            $user = $this->entityManager->getRepository($entity)->findOneBy(['email' => $email]);
            if ($user) {
                return $user;
            }
        }
        return null;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $email = $token->getUser()->getUserIdentifier(); // getUserIdentifier() method returns the username (email in this case)
        $request->getSession()->set('userEmail', $email);

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
        // Provide a default redirection if no target path is set, adjust as needed
        if (in_array('ROLE_CLIENT', $token->getRoleNames())) {
            return new RedirectResponse($this->urlGenerator->generate('app_clienthome'));
        }
        if (in_array('ROLE_RESPONSIBLE_ACCOUNT', $token->getRoleNames())) {
            return new RedirectResponse($this->urlGenerator->generate('app_responsible_account_home'));
        }
        if (in_array('ROLE_RESPONSIBLE_LOAN', $token->getRoleNames())) {
            return new RedirectResponse($this->urlGenerator->generate('app_responsible_loan_home'));
        }
        if (in_array('ROLE_RESPONSIBLE_CLIENTELE', $token->getRoleNames())) {
            return new RedirectResponse($this->urlGenerator->generate('app_responsible_clientele_home'));
        }
        if (in_array('ROLE_RESPONSIBLE_INVESTMENT', $token->getRoleNames())) {
            return new RedirectResponse($this->urlGenerator->generate('app_responsible_investment_home'));
        }
        throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
