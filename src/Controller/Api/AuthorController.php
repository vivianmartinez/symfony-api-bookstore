<?php

namespace App\Controller\Api;

use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class AuthorController extends AbstractFOSRestController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Rest\Get('/authors', name: 'app_authors')]
    #[Rest\View(serializerGroups:['author'],serializerEnableMaxDepthChecks:true)]
    public function index(): JsonResponse
    {
        $authors = $this->em->getRepository(Author::class)->findAll();
        return $this->json($authors,Response::HTTP_OK,[],[AbstractNormalizer::ATTRIBUTES=>['id','name','books'=>['id','title']]]);
    }
}
