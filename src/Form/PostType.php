<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    $builder
        ->add('name', TextType::class, [
            'attr' => [
                'class'       => 'form-control w-50',
                'placeholder' => 'Enter Name'
            ]])
        ->add('body', TextareaType::class, [
            'attr' => [
                'class'       => 'form-control w-50',
                'placeholder' => 'Enter Name'
            ]])
        ->add('category', EntityType::class, [
                'class' => Category::class,
                'attr' => [
                    'class'       => 'form-control w-50',
                    'placeholder' => 'Select'
                ]
            ])
        ->add('image', FileType::class, [
            'attr' => [
                'class'       => 'form-control w-25',
            ],
            'label' => 'Upload an image',
            'mapped' => false,
            'required' => true,
        ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
