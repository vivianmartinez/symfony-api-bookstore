<?php

namespace App\Form;

use App\Entity\Book;
use App\Form\Model\BookDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, array('constraints'=>[new NotBlank(),new Length(
                                            ['min' => 5,
                                            'max' => 250,
                                            'minMessage'=>'The title must be at least 5 characters long',
                                            'maxMessage'=>'The title cannot be longer than 250 characters'])])) 
            //validation not blank in config/validator/Book.yaml
            ->add('description', TextType::class)
            ->add('price', NumberType::class,array('constraints'=>[new NotBlank()]))
            ->add('imageBase64', TextType::class)
            ->add('author', IntegerType::class, array('constraints'=>[new NotBlank()]))
            ->add('category',IntegerType::class, array('constraints'=>[new NotBlank()]))
            ->add('tags', CollectionType::class,
                array(
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_type' => TagType::class
                )
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //change here to use BookDto class
            'data_class' => BookDto::class,
            'csrf_protection' => false
            //'data_class' => Book::class,
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
