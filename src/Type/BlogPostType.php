<?php

namespace App\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', textType::class, [
                'label' => 'Title'
            ])
            ->add('description', textType::class, [
                'label' => 'Description',
                'required' => false
            ])
            ->add('created_at', dateType::class, [
                'label' => 'Created at'
            ])
            ->add('additional_links', textType::class, [
                'label' => 'Additional links',
                'required' => false
            ])
            ->add('category', BlogCategory::class, [
                'label' => 'Category'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BlogPost::class,
        ]);
    }
}
