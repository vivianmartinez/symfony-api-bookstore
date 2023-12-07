<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;


class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'csrf_protection' => false,
            'constraints'=>[
                new UniqueEntity([
                    'entityClass' => Category::class,
                    'fields' => 'name'
                    ])
            ]
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

    public function getName(){
        return '';
    }
}
