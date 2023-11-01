<?php

namespace App\Controller\Api;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;



class CategoryController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Rest\Get('/categories', name: 'app_categories')]
    #[Rest\View(serializerGroups:['category'],serializerEnableMaxDepthChecks:true)]
    public function index(): JsonResponse
    {
        $categories = $this->em->getRepository(Category::class)->findAll();
        //$context = [AbstractNormalizer::ATTRIBUTES => ['id','name','books'=>['id','title','description','picture','price','author'=>['id','name']]]];
        return $this->json($categories,Response::HTTP_OK,[],['groups'=>'category']);
    }
}
