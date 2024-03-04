<?php
namespace App\Entity\transactions;

// src/Form/SendMoneyFormType.php


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints as Assert;

class SendMoneyFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('account', ChoiceType::class, [
                'label' => 'Choose Account',
                'label_attr' => ['class' => 'form-label','style' => 'font-size: 1em;color: #1ab11a;'],
                'choices' => [
                   'SAVINGS' => 'SAVINGS',
                     'JOINT' => 'JOINT',
                    'CHECKING' => 'CHECKING',
                    'STUDENT' => 'STUDENT',
                    'BUSINESS' => 'BUSINESS',
                ],
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('amount', IntegerType::class, [
                'label' => 'Amount',
                'label_attr' => ['class' => 'form-label','style' => 'font-size: 1em;color: #1ab11a;'],
                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern' => '/^\d+$/',
                        'message' => 'Please enter a valid amount (numeric characters only).',
                    ]),
                    new Assert\GreaterThan(0)
                ],
            ])
            ->add('recipient', TextType::class, [
                'label' => 'Address',
                'label_attr' => ['class' => 'form-label','style' => 'font-size: 1em;color: #1ab11a;'],
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2, 'max' => 255]),
                ],
            ])
            ->add('codeAuthenticator', TextType::class, [
                'label' => 'Authenticator',
                'label_attr' => ['class' => 'form-label','style' => 'font-size: 1em;color: #1ab11a;'],
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 6, 'max' => 6]),
                    new Regex([
                        'pattern' => '/^\d+$/',
                        'message' => 'Please enter a 6-digit numeric code.',
                    ]),
                ],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

