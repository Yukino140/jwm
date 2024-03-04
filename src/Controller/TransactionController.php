<?php

namespace App\Controller;

use App\Entity\transactions\SendMoneyFormType;
use App\Entity\transactions\Transaction;
use App\Repository\AccountRepository;
use App\Repository\TransactionRepository;
use App\services\AppExtension;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction')]
class TransactionController extends AbstractController
{
    /**
        * this could be the add transaction page and the form to add a transaction
     */
    #[Route('/add', name: 'app_add_transaction')]
    public function add(Request $request,AppExtension $appExtension,AccountRepository $accountRepository): Response
    {
        $form = $this->createForm(SendMoneyFormType::class);

        $clientAccountTypes = [];
        $clientAccounts = $appExtension->getClient()->getListAccount()->toArray();
        foreach ($clientAccounts as $account) {
            // Assuming getAccountType() returns the account type
            // Adjust this according to your entity structure
            $clientAccountTypes[] = $account->getAccountType()->value;
        }

        dump($clientAccountTypes);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $response = '';

            $formData = $form->getData();

            $transaction = new Transaction();
            $transaction->setAccountNumber('');
            $transaction->setTransactionType($formData['account']);
            $transaction->setAmount($formData['amount']);
            $transaction->setCreatedAt(new \DateTimeImmutable());
            $transaction->setDescription('');
            $transaction->setAuthenticatorCode(0);
            $transaction->setReceiverAccountNumber($formData['recipient']);
            if($formData['account'] == 'SAVINGS'){
                $transaction->setFee(0.02);
            }elseif($formData['account'] == 'JOINT'){
                $transaction->setFee(0.03);
            }elseif($formData['account'] == 'CHECKING'){
                $transaction->setFee(0.04);
            }elseif($formData['account'] == 'STUDENT'){
                $transaction->setFee(0.01);
            }elseif($formData['account'] == 'BUSINESS'){
                $transaction->setFee(0.06);
            }

            $accounts = $appExtension->getClient()->getListAccount()->toArray();
            dump($accounts);
            foreach ($accounts as $account){
                if($account->getAccountType()->value == $formData['account']){
                    $transaction->setAccountNumber($account->getAccountNumber());
                }
            }


            if ($transaction->getAmount()>$accountRepository->getBalance($transaction->getAccountNumber())){
                $response = 'Insufficient balance 😔';
                //reset the form
                $form = $this->createForm(SendMoneyFormType::class);
                dump($response);
                return $this->render('transaction/add_transaction.html.twig', [
                    'controller_name' => 'TransactionController',
                    'form' => $form->createView(),
                    'response' => $response,
                     'clientAccountTypes' => $clientAccountTypes
                ]);
            }

            $existRecipient = $accountRepository->findOneBy(['account_number' => $formData['recipient']]);
            if ($existRecipient == null){
                $response = 'Recipient Account not found ⚠️';
                // reset the form
                $form = $this->createForm(SendMoneyFormType::class);
                dump($response);
                return $this->render('transaction/add_transaction.html.twig', [
                    'controller_name' => 'TransactionController',
                    'form' => $form->createView(),
                    'response' => $response,
                    'clientAccountTypes' => $clientAccountTypes,
                ]);
            }


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($transaction);
            $entityManager->flush();

            $account_sender = $accountRepository->findOneBy(['account_number' => $transaction->getAccountNumber()]);
            $account_reciver = $accountRepository->findOneBy(['account_number' => $transaction->getReceiverAccountNumber()]);

            // send exchange api request
            $apiKey = '016b7faed77795b0f5431c17';
            $baseCurrency = strtoupper($account_sender->getCurrency()->value);
            $targetCurrency = strtoupper($account_reciver->getCurrency()->value);

            $url = "https://v6.exchangerate-api.com/v6/{$apiKey}/pair/{$baseCurrency}/{$targetCurrency}";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);

            if(curl_errno($ch)){
                throw new Exception(curl_error($ch));
            }

            curl_close($ch);

            $exchangeRateData = json_decode($response, true);
            $exchangeRate = $exchangeRateData['conversion_rate'];



            $account_sender->setBalance($account_sender->getBalance() - $transaction->getAmount());
            $account_reciver->setBalance($account_reciver->getBalance() + ($transaction->getAmount() * $exchangeRate));

            $entityManager->persist($account_sender);
            $entityManager->persist($account_reciver);
            $entityManager->flush();
            // reset the form
            $form = $this->createForm(SendMoneyFormType::class);
            $response = 'Transaction completed successfully 🎉';

        }

        return $this->render('transaction/add_transaction.html.twig', [
            'controller_name' => 'TransactionController',
            'form' => $form->createView(),
            'clientAccountTypes' => $clientAccountTypes,
        ]);
    }







    /**
     * this could be the list of all transactions ,
     * pagination could be added to this page and also a search bar to search for a transaction ,
     * facture or a transaction type
     */
    #[Route('/show_all', name: 'app_transaction_list')]
    public function list(TransactionRepository $transactionRepository,AppExtension $appExtension): Response
    {
        $myAccounts = $appExtension->getClient()->getListAccount()->toArray();
        $myTransactions = [];
        foreach ($myAccounts as $account){
            $myTransactions = array_merge($myTransactions,$transactionRepository->findByAccountNumber($account->getAccountNumber()));
        }
        dump($myTransactions);

        return $this->render('transaction/all_transations.html.twig', [
            'controller_name' => 'TransactionController',
            'transactions' => $myTransactions
        ]);
    }












    /**
     * this could be the page to manage the transaction categories
     */

    #[Route('/manage', name: 'app_manage_transaction')]
    public function manage(): Response
    {
        return $this->render('transaction/manage_categories.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }
}
