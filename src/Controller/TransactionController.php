<?php

namespace App\Controller;

use App\Entity\transactions\Facture;
use App\Entity\transactions\SendMoneyFormType;
use App\Entity\transactions\Transaction;
use App\Repository\AccountRepository;
use App\Repository\CompteRepository;
use App\Repository\FactureRepository;
use App\Repository\TransactionRepository;
use App\services\AppExtension;
use Doctrine\Persistence\ManagerRegistry;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            $transaction->setDate(new \DateTime('now'));
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
    public function list(TransactionRepository $transactionRepository,AppExtension $appExtension,Request $req):Response
    {
        $myAccounts = $appExtension->getClient()->getListAccount()->toArray();
        $myTransactions = [];
        $date1=$req->get('date1');
        $date2=$req->get('date2');
        foreach ($myAccounts as $account){
            $myTransactions = array_merge($myTransactions,$transactionRepository->findByAccountNumber($account->getAccountNumber(),$date1,$date2));
        }
        dump($myTransactions);
        if($req->get('ajax')){
            return new JsonResponse([
                'content'=>$this->renderView('transaction/tableTransactionsClient.html.twig',[
                    'transactions' => $myTransactions,
                    'date1'=>$date1,
                    'date2'=>$date2


                ]),
                'data'=>$this->renderView('Receipt/ReceiptTotal.html.twig',[
                    'transactions' => $myTransactions,
                    'date1'=>$date1,
                    'date2'=>$date2,
                    ])

            ]);
        }


        return $this->render('transaction/all_transations.html.twig', [
            'controller_name' => 'TransactionController',
            'transactions' => $myTransactions,
            'date1'=>$date1,
            'date2'=>$date2,
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

    //Add Facture methode
    #[Route('addFacture/{id}',name:'addF')]
    public function addFacture(Request $req, ManagerRegistry $mg,TransactionRepository $rep,$id):Response
    {
        $em = $mg->getManager();
        $facture=new Facture();
        $transaction=$rep->find($id);
        $facture->setIdTransaction($transaction);

            $facture->setTax(1);
            $facture->setMontantTTC($transaction->getAmount()-($transaction->getAmount()*0.01));



        $em->persist($facture);
        $em->flush();
        return $this->redirectToRoute('app_transaction_list',[
            "tran"=>$transaction,
            "fac"=>$facture
        ]);

    }


    //show Facture By idTransaction
    #[Route('showFact/{id}',name:'showF')]
    public function showFa($id,FactureRepository $repo,TransactionRepository $rep):Response
    {
        $fact=$repo->findByIDTransaction($id);
        $tranc=$rep->find($id);
        return $this->render('Receipt/Receipt.html.twig',['tranc'=>$tranc,'fact'=>$fact]);
    }


    //Exportation in PDF File

    #[Route('/pdf/{id}',name:'pdf')]
    public function pdfgenerate(Request $req,$id,FactureRepository $repo,TransactionRepository $rep):Response
    {
        $pdfOption = new Options();
        $pdfOption->set('defaultFont','Arial');
        $pdfOption->setIsRemoteEnabled(true);

        $dompdf=new Dompdf($pdfOption);
        $context= stream_context_create([
            'ssl' => [
                'verify_peer'=>False,
                'verify_peer_name'=>False,
                'allow_self_signed'=>True
            ]
        ]);
        $fact=$repo->findByIDTransaction($id);
        $tranc=$rep->find($id);
        $compte=$tranc->getAccountNumber();
        $dompdf->setHttpContext($context);
        $html=$this->renderView('Receipt/ReceiptTable.html.twig',['tranc'=>$tranc,'fact'=>$fact]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();
        $fichier='facture°'.$fact->getId().'.pdf';

        $dompdf->stream($fichier,[
            'Attachement'=>true
        ]);
        return new Response();
    }

    //delete a Transaction By ID

    #[Route('deleteT/{id}',name:'deleteT')]
    public function deleteT($id,TransactionRepository $repo,FactureRepository $rep,ManagerRegistry $mg):Response
    {
        $facture=$rep->findByIDTransactionOrNot($id);
        $transaction=$repo->find($id);
        $em=$mg->getManager();
        if($facture!=null){
            $em->remove($facture);
            $em->flush();
        }
        $em->remove($transaction);
        $em->flush();
        return $this->redirectToRoute('app_transaction_list');

    }

    //delete a Receipt by ID

    #[Route('deleteF/{id}',name:'deleteF')]
    public function deleteF($id,FactureRepository $rep,ManagerRegistry $mg,TransactionRepository $repo):Response
    {
        $facture=$rep->find($id);
        $em=$mg->getManager();
        $em->remove($facture);
        $em->flush();
        return $this->redirectToRoute('app_transaction_list');
    }

    #[Route('/excel',name:'ExportExcel')]
    public function exportExcel(Request $req,TransactionRepository $rep,AppExtension $appExtension)
    {
        $myAccounts = $appExtension->getClient()->getListAccount()->toArray();

        $date1=$req->get('date1');
        $date2=$req->get('date2');
        $myTransactions=$rep->findByAccountNumber($date1,$date2);
        $filename="data".$date1."=>".$date2.".xls";
        $fileds=array('ID','TypeTransaction','ReceiverAccount','Amount');
        $excelData = implode("\t",array_values($fileds))."\n";
        foreach ($myTransactions as $t){
            $lineData =array($t->getId(),$t->getTransactionType(),$t->getReceiverAccountNumber(),$t->getAmount());
            $excelData .=implode("\t",array_values($lineData))."\n";

        }
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachement; filename=\"$filename\"");
        echo $excelData;
        exit();
    }
}
