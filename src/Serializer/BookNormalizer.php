<?php

// src/Serializer/BookNormalizer.php
namespace App\Serializer;

use App\Entity\Book;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class BookNormalizer implements NormalizerInterface
{
    public function __construct(
        private UrlGeneratorInterface $router,
        private ObjectNormalizer $normalizer,
        private UrlHelper $urlHelper
    ) {
    }

    public function normalize($book, string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($book, $format, $context);

        // Here, add, edit, or delete some data: 

        if($book->getPicture()){
            $data['picture'] = $this->urlHelper->getAbsoluteUrl('/storage/book_cover/'.$book->getPicture());
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Book;
    }
}
