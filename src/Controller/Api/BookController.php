<?php

namespace App\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;



class BookController extends AbstractFOSRestController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    //Route list all books
    
    #[Rest\Get('/books', name: 'app_books')]
    #[Rest\View(serializerGroups:['book'],serializerEnableMaxDepthChecks:true)]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome api books',
        ]);
    }
}
