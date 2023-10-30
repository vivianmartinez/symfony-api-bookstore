<?php

namespace App\Controller\Api;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;


class BookController extends AbstractFOSRestController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    //Route list all books

    #[Rest\Get(path:'/books', name: 'app_books')]
    #[Rest\View(serializerGroups:['book'],serializerEnableMaxDepthChecks:true)]
    public function index(BookRepository $bookRepository): JsonResponse
    {
        $books = $bookRepository->findAll();
        return $this->json($books,Response::HTTP_OK,[],[AbstractNormalizer::ATTRIBUTES => ['id', 'title','description','picture', 'author'=>['id','name'],'category'=>['id','name']]]);
    }

    //Create book
    #[Rest\Post(path:'/book/create',name:'app_book_create')]
    #[Rest\View(serializerGroups:['book'],serializerEnableMaxDepthChecks:true)]
    public function createBook(Request $request)
    {
        $book = new Book();
        $form = $this->createForm(BookType::class,$book);
        $form->handleRequest($request);
        if(!$form->isSubmitted()){
            return new Response('You must send data', Response::HTTP_BAD_REQUEST);
        }

        if($form->isValid())
        {
            $book->setCreatedAt(new \DateTime('now'));
            $this->em->persist($book);
            $this->em->flush();
    
            return $this->json($book,Response::HTTP_OK,[],[AbstractNormalizer::ATTRIBUTES =>['id','title','description','picture','author'=>['id','name'],'category'=>['id','name'],'createdAt']]);
        }
        return $form;      
    }
}
