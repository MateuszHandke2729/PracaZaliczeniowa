<?php

namespace App\Form;

use App\Entity\BlogPolls;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogPollsVoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('answers', ChoiceType::class, [
                'choices'  => [
                    'answer1' => null,
                    'answer2' => true,
                    'answer3' => false,
                ],
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('Vote',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BlogPolls::class,
        ]);
    }
}
