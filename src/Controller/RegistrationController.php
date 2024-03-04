<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Client;
use App\Entity\enum\AccountType;
use App\Entity\enum\Currency;
use App\Entity\ResponsibleAccount;
use App\Entity\ResponsibleClientele;
use App\Entity\ResponsibleInvestment;
use App\Entity\ResponsibleLoan;
use App\services\UniqueStringGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route('/initClients', name: 'initClients')]
    public function init(UserPasswordEncoderInterface $encoder): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/dummy_data.json';
        $jsonContent = file_get_contents($filePath);

        if (!$jsonContent) {
            return new Response('Failed to read dummy data file.', 500);
        }

        $data = json_decode($jsonContent, true);
        foreach ($data['CLIENTS'] as $clientData) {
            $client = $this->getClient($clientData, $encoder);
            $client->setBirthDate(new \DateTime($clientData['birth_date']));
            $client->setAddress($clientData['address']);
            $client->setTransactionLimit($clientData['transaction_limit']);
            $client->setAccountType($clientData['account_type']);
            $client->setCreatedAt(new \DateTimeImmutable());

            $em = $this->getDoctrine()->getManager();
            $em->persist($client);
            $em->flush();
        }

        return new Response('clients created');
    }

    #[Route('/initAgents', name: 'initAgents')]
    public function initAgents(UserPasswordEncoderInterface $encoder): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/dummy_data.json';
        $jsonContent = file_get_contents($filePath);

        if (!$jsonContent) {
            return new Response('Failed to read dummy data file.', 500);
        }

        $data = json_decode($jsonContent, true);
        if (isset($data['AGENTS'])) {
            foreach ($data['AGENTS'] as $agentType => $agentData) {
                switch ($agentType) {
                    case 'responsible_accounts':
                        foreach ($agentData as $responsibleAccountData) {
                            $responsibleAccount = new ResponsibleAccount();
                            $responsibleAccount->setEmail($responsibleAccountData['email']);
                            $responsibleAccount->setPassword($encoder->encodePassword($responsibleAccount, $responsibleAccountData['password']));
                            $responsibleAccount->setRoles(['ROLE_RESPONSIBLE_ACCOUNT']);
                            $responsibleAccount->setFirstName($responsibleAccountData['first_name']);
                            $responsibleAccount->setLastName($responsibleAccountData['last_name']);
                            $responsibleAccount->setPhoneNumber($responsibleAccountData['phone_number']);
                            $responsibleAccount->setCin($responsibleAccountData['cin']);

                            $em = $this->getDoctrine()->getManager();
                            $em->persist($responsibleAccount);
                        }
                        break;
                    // Add cases for other agent types here if needed
                    case 'responsible_investment':
                        foreach ($agentData as $responsibleInvestmentData) {
                            // Create responsible investment agents here
                            $responsibleInvestment = new ResponsibleInvestment();
                            $responsibleInvestment->setEmail($responsibleInvestmentData['email']);
                            $responsibleInvestment->setPassword($encoder->encodePassword($responsibleInvestment, $responsibleInvestmentData['password']));
                            $responsibleInvestment->setRoles(['ROLE_RESPONSIBLE_INVESTMENT']);
                            $responsibleInvestment->setFirstName($responsibleInvestmentData['first_name']);
                            $responsibleInvestment->setLastName($responsibleInvestmentData['last_name']);
                            $responsibleInvestment->setPhoneNumber($responsibleInvestmentData['phone_number']);
                            $responsibleInvestment->setCin($responsibleInvestmentData['cin']);

                            $em = $this->getDoctrine()->getManager();
                            $em->persist($responsibleInvestment);

                        }
                        break;
                    case 'responsible_clientele':
                        foreach ($agentData as $responsibleClienteleData) {
                            // Create responsible clientele agents here
                            $responsibleClientele = new ResponsibleClientele();
                            $responsibleClientele->setEmail($responsibleClienteleData['email']);
                            $responsibleClientele->setPassword($encoder->encodePassword($responsibleClientele, $responsibleClienteleData['password']));
                            $responsibleClientele->setRoles(['ROLE_RESPONSIBLE_CLIENTELE']);
                            $responsibleClientele->setFirstName($responsibleClienteleData['first_name']);
                            $responsibleClientele->setLastName($responsibleClienteleData['last_name']);
                            $responsibleClientele->setPhoneNumber($responsibleClienteleData['phone_number']);
                            $responsibleClientele->setCin($responsibleClienteleData['cin']);

                            $em = $this->getDoctrine()->getManager();
                            $em->persist($responsibleClientele);
                        }
                        break;
                    case 'responsible_loan':
                        foreach ($agentData as $responsibleLoanData) {
                            // Create responsible loan agents here
                            $responsibleLoan = new ResponsibleLoan();
                            $responsibleLoan->setEmail($responsibleLoanData['email']);
                            $responsibleLoan->setPassword($encoder->encodePassword($responsibleLoan, $responsibleLoanData['password']));
                            $responsibleLoan->setRoles(['ROLE_RESPONSIBLE_LOAN']);
                            $responsibleLoan->setFirstName($responsibleLoanData['first_name']);
                            $responsibleLoan->setLastName($responsibleLoanData['last_name']);
                            $responsibleLoan->setPhoneNumber($responsibleLoanData['phone_number']);
                            $responsibleLoan->setCin($responsibleLoanData['cin']);

                            $em = $this->getDoctrine()->getManager();
                            $em->persist($responsibleLoan);
                        }
                        break;

                }
            }
            $em->flush(); // Flush outside the loop to persist all entities once
            return new Response('<p>Agents created successfully.</p>');
        } else {
            return new Response('<p>No agents found in the data.</p>');
        }
    }




    #[Route('/signup', name: 'register')]
    public function register(Request $request,
                             UserPasswordEncoderInterface $encoder,
                             ValidatorInterface $validator,
                             UniqueStringGenerator $uniqueStringGenerator): Response
    {
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter your email address.',
                    ]),
                    new Assert\Email([
                        'message' => 'The email {{ value }} is not a valid email.',
                    ]),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options'  => [
                    'label' => 'Password',
                    'constraints' => [
                        new Assert\NotBlank([
                            'message' => 'Please enter a password.',
                        ]),
                        new Assert\Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // You can also define a max length
                            // 'max' => 20,
                        ]),
                        // Uncomment the line below if you want to enforce a strong password policy
                        // new Assert\Regex([
                        //     'pattern' => '/[A-Z]+.*[0-9]+/',
                        //     'message' => 'Your password should contain at least one uppercase letter and one number.',
                        // ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirm Password',
                    'constraints' => [
                        new Assert\NotBlank([
                            'message' => 'Please confirm your password.',
                        ]),
                        // The Length constraint is automatically applied to this field as well
                    ],
                ],
                'invalid_message' => 'The password fields must match.',
                // Enable this option if you want to force the user to type the password again instead of just copying and pasting
                // 'options' => array('attr' => array('autocomplete' => 'new-password')),
                // If you want to enable server-side validation of the fields matching, you can use the following option
                // 'constraints' => new Assert\EqualTo([
                //     'propertyPath' => 'first_options[data]',
                //     'message' => 'The password fields must match.',
                // ]),
            ])
            ->add('first_name', null, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter your first name.',
                    ]),
                ],
            ])
            ->add('last_name', null, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter your last name.',
                    ]),
                ],
            ])
            ->add('phone_number', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter your phone number.',
                    ]),
                    new Assert\Length([
                        'min' => 8,
                        'max' => 8,
                        'exactMessage' => 'The phone number must contain exactly {{ limit }} digits.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]{8}$/',
                        'message' => 'The phone number must contain exactly 8 digits.',
                    ]),
                ],
            ])
            ->add('cin', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter your carte identity number.',
                    ]),
                    new Assert\Length([
                        'min' => 8,
                        'max' => 8,
                        'exactMessage' => 'The cin number must contain exactly {{ limit }} digits.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]{8}$/',
                        'message' => 'The cin number must contain exactly 8 digits.',
                    ]),
                ],
            ])
            ->add('birth_date', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter your birth date.',
                    ]),
                    new Assert\LessThan([
                        'value' => 'today',
                        'message' => 'The birth date must be in the past.',
                    ]),
                ],
            ])

            ->add('address', null, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter your address.',
                    ]),
                ],
            ])
            ->add('transaction_limit', NumberType::class, [
                // 'scale' => 2, // Use this if you want to allow decimals
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter a transaction limit.',
                    ]),
                    new Assert\Range([
                        'min' => 10,
                        'max' => 10000,
                        'minMessage' => 'The transaction limit must be at least {{ limit }}.',
                        'maxMessage' => 'The transaction limit cannot exceed {{ limit }}.',
                    ]),
                ],
            ])
            ->add('account_type', ChoiceType::class, [
                'choices' => [
                    'STUDENT' => 'STUDENT',
                    'CHECKING' => 'CHECKING',
                    'SAVINGS' => 'SAVINGS',
                    'JOINT' => 'JOINT',
                    'BUSINESS' => 'BUSINESS',
                    // Add more account types as needed
                ],
                'required' => true,
            ])


            ->add('register', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success float-right']
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()&& $form->isValid()){
            $data = $form->getData();

            $client = $this->getClient($data, $encoder);
            $client->setBirthDate($data['birth_date']);
            $client->setAddress($data['address']);
            $client->setTransactionLimit($data['transaction_limit']);
            $client->setAccountType($data['account_type']);
            $client->setCreatedAt(new \DateTimeImmutable());

            dump($client);

            $em = $this->getDoctrine()->getManager();
            $em->persist($client);
            $em->flush();

            $account = new Account();
            $uniqueAccountNumber = $uniqueStringGenerator->generateUniqueString(20);
            $account->setAccountNumber($uniqueAccountNumber);

            $accountTypeData = $data['account_type'];
            $accountTypeEnum = AccountType::from($accountTypeData);
            $account->setAccountType($accountTypeEnum);

            $account->setCurrency(Currency::class::TND);
            $account->setValidate(false);
            $account->setBalance(0);
            $account->setCreatedAt(new \DateTimeImmutable());
            $account->setOwner($client);

            $em->persist($account);
            $em->flush();

            return $this->redirectToRoute('app_login');
        }else{
            $errors = $validator->validate($form);
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param mixed $clientData
     * @param UserPasswordEncoderInterface $encoder
     * @return Client
     */
    public function getClient(mixed $clientData, UserPasswordEncoderInterface $encoder): Client
    {
        $client = new Client();
        $client->setEmail($clientData['email']);
        $client->setPassword($encoder->encodePassword($client, $clientData['password']));

        $client->setRoles(['ROLE_CLIENT']);
        $client->setFirstName($clientData['first_name']);
        $client->setLastName($clientData['last_name']);
        $client->setPhoneNumber($clientData['phone_number']);
        $client->setCin($clientData['cin']);
        return $client;
    }
}
