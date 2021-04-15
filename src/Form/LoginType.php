<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('username')
            ->add('firstname')
            ->add('name')
            ->add('email')
            ->add('password', PasswordType::class)
            ->add('birthdate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('submit', SubmitType::class)
            


            ->add('username', TextType::class, [
                'required' => true,
                'label' => 'Pseudo',
            ])
            ->add('firstname', TextType::class, [
                'required' => true,
                'label' => 'Nom',
            ])
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Prénom',
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'Adresse Email',
            ])
            ->add('password', PasswordType::class, [
                'required' => true,
                'label' => 'Mot de passe',
            ])
            ->add('birthdate', DateType::class, [
                'required' => true,
                'label' => 'Date de naissance',
                'widget' => 'single_text',

            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn-theme mt-3'
                ]
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
