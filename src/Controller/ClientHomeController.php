<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\enum\AccountType;
use App\Entity\enum\Currency;
use App\Repository\ClientRepository;
use App\services\AppExtension;
use App\services\UniqueStringGenerator;
use phpDocumentor\Reflection\PseudoTypes\LowercaseString;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/client', name: 'app_client')]
class ClientHomeController extends AbstractController
{


    #[Route('/home', name: 'home')]
    public function index(Session $session,ClientRepository $clientRepository): Response
    {
        return $this->render('clientdash/index.html.twig', [
            'controller_name' => 'ClientdashController',
        ]);
    }

    #[Route('/dashboard',name:'dashboard')]
    public function indexclient(AppExtension $appExtension):Response
    {
        $list_account = $appExtension->getClient()->getListAccount()->toArray();
        dump($list_account);
        $total_balance = 0;
        foreach ($list_account as $account){
            dump($account->getAccountType());
            $total_balance += $account->getBalance();
        }
        return $this->render('clientdash/client/client.html.twig',[
            'list_account' => $list_account,
            'total_balance' => $total_balance
        ]);
    }

    #[Route('/my_accounts',name:'accounts')]
    public function allAccounts(AppExtension $appExtension):Response{

        $list_account = $appExtension->getClient()->getListAccount()->toArray();
        //dump($list_account);
        dump($appExtension->getClient());
        return $this->render('clientdash/accounts/client-accounts.html.twig',[
            'list_account' => $list_account
        ]);
    }

    #[Route('/create_account',name:'create_account',methods: ['POST'])]
    public function createAccount(Request $request,ClientRepository $clientRepository,
                                  UniqueStringGenerator $uniqueStringGenerator,
                                  AppExtension $appExtension,SerializerInterface $serializer):Response{
        $data = json_decode($request->getContent(), true);
        $accountType = $data['accountType'];
        $currency = $data['currency'];

        dump($accountType);

        $account = new Account();
        $uniqueAccountNumber = $uniqueStringGenerator->generateUniqueString(20);
        $account->setAccountNumber($uniqueAccountNumber);

        // Remove the word "Account"
        $accountType = str_replace("Account", "", $accountType);

        // Trim any remaining whitespace at the beginning and end
        $accountType = trim($accountType);

        $accountTypeEnum = AccountType::from($accountType."");
        $account->setAccountType($accountTypeEnum);

        $lowercaseString = strtolower($currency."");
        $accountCurrency = Currency::from($lowercaseString."");
        $account->setCurrency($accountCurrency);
        $account->setValidate(false);
        $account->setBalance(0);
        $account->setCreatedAt(new \DateTimeImmutable());
        if ($appExtension->getClient() == null){
            return $this->json(['status' => 'error', 'message' => 'Client not found!']);
        }else{
            $client = $appExtension->getClient();
            $account->setOwner($client);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($account);
            $entityManager->flush();
        }

        $jsonContent = $serializer->serialize($account, 'json', [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId(); // Return a unique identifier for the object instead
            },
        ]);

        return $this->json(['status' => 'success', 'message' => 'Account created successfully!', 'account' => json_decode($jsonContent)]);

    }

    #[Route('/my_accounts/dash_account',name:'dash_account')]
    public function dashAccount(Request $request,
                                AppExtension $appExtension):Response{
        $accountId = $request->query->get('accountId');
        $connectedClient = $appExtension->getClient();
        $accounts_list = $connectedClient->getListAccount()->toArray();

        $filtered_data = array_filter($accounts_list, function($account) use ($accountId){
            return $account->getAccountNumber() == $accountId;
        });
        dump($filtered_data);
        $account_data = reset($filtered_data);
        dump($account_data);

        if ($account_data == null){
            $account_data = $accounts_list[0];
        }


        return $this->render('clientdash/accounts/dashboard_compte.html.twig',[
            'account' => $account_data
        ]);
    }

    #[Route('/my_accounts/get_account_data/{account_id}', name: 'get_account_data')]
    public function getAccountData(SerializerInterface $serializer, string $account_id): Response
    {
        $account = $this->getDoctrine()->getRepository(Account::class)->find($account_id);
        dump($account);
        $jsonContent = $serializer->serialize($account, 'json', [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId(); // Return a unique identifier for the object instead
            },
        ]);
        return $this->json(['status' => 'success', 'account' => json_decode($jsonContent)]);
    }


    #[Route('/profile',name:'profile')]
    public function indexclientPro():Response
    {
        return $this->render('clientdash/profile/client-profile.html.twig');
    }

    #[Route('/contact',name:'contact')]
    public function indexadminContact():Response
    {
        return $this->render('clientdash/contact/client-contact.html.twig');
    }
}
