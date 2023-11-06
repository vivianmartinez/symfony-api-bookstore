<?php

namespace App\Form;

use App\Entity\Tag;
use App\Form\Model\TagDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id')
            ->add('name',TextType::class,array('constraints'=>[new NotBlank(), new Length([
                                                                'min'=> 5,
                                                                'max'=> 250,
                                                                'minMessage'=>'The name must be at least 5 characters long','maxMessage'=>'The name cannot be longer than 250 characters'
                                                                ])]))
            //->add('books')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TagDto::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

    public function getName()
    {
        return '';
    }
}
