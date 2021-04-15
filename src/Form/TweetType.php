<?php

namespace App\Form;

use App\Entity\Tweet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class TweetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', TextType::class, array(
                'constraints' => array(
                    new Length([
                        "max" => 144
                    ])
                ),
                'attr' => [
                    'placeholder' => 'Quoi de neuf ?'
                ],
                'label' => false,
            ))
            ->add('image', FileType::class, array(
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => 'file_input '
                ],
            ))

            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn-theme '
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tweet::class,
        ]);
    }
}
