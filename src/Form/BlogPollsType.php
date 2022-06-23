<?php

namespace App\Form;

use App\Entity\BlogPolls;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogPollsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question')
            ->add('answer1',TextType::class,[
                'mapped'=>false,
            ])
            ->add('answer2',TextType::class,[
            'mapped'=>false,
                ])
            ->add('answer3',TextType::class,[
                'mapped'=>false,
            ])
            ->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BlogPolls::class,
        ]);
    }
}
