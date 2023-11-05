<?php

namespace App\Validator;

use App\Repository\AuthorRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueAuthorValidator extends ConstraintValidator
{
    private $authorRepository;
    public function __construct(AuthorRepository $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\UniqueAuthor $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $author = $this->authorRepository->findOneBy(['name' => $value]);

        if(!$author){
            return;
        } 

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
