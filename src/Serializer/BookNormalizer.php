<?php

// src/Serializer/BookNormalizer.php
namespace App\Serializer;

use App\Entity\Book;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;



class BookNormalizer implements NormalizerInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlRouter,
        private ObjectNormalizer $normalizer,
        private UrlHelper $urlHelper,
        private RouterInterface $router,
    ) {
    }

    public function normalize($book, string $format = null, array $context = []): array
    {
               
        $data = $this->normalizer->normalize($book, $format, $context);

        // Here, add, edit, or delete some data: 

        if($book->getPicture()){
            //$data['title'] = $book->getTitle();
            $data['picture'] = $this->urlHelper->getAbsoluteUrl('/storage/book_cover/'.$book->getPicture());
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Book;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            'object' => null,             // Doesn't support any classes or interfaces
            '*' => false,                 // Supports any other types, but the result is not cacheable
            Book::class => true, // Supports MyCustomClass and result is cacheable
        ];
    }
}
