<?php

namespace App\Controller\Api;

use App\Entity\Book;
use App\Form\BookType;
use App\Form\Model\BookDto;
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
    private $authorRepository;
    private $categoryRepository;

    public function __construct(EntityManagerInterface $em, AuthorRepository $authorRepository, CategoryRepository $categoryRepository )
    {
        $this->em = $em;
        $this->authorRepository   = $authorRepository;
        $this->categoryRepository = $categoryRepository;
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
        $bookDto = new BookDto();
        $form = $this->createForm(BookType::class,$bookDto);
        $form->handleRequest($request);
        if(!$form->isSubmitted()){
            return new Response('Empty data', Response::HTTP_BAD_REQUEST);
        }

        if($form->isValid())
        {
            $book = new Book();
            $book->setTitle($bookDto->title);
            $book->setDescription($bookDto->description);
            $book->setPicture($bookDto->picture);
            $book->setPrice($bookDto->price);
            $author   = $this->authorRepository->find($bookDto->author);
            $category = $this->categoryRepository->find($bookDto->category);
            $book->setAuthor($author);
            $book->setCategory($category);
            $book->setCreatedAt(new \DateTime('now'));
            $this->em->persist($book);
            $this->em->flush();
    
            return $this->json($book,Response::HTTP_OK,[],[AbstractNormalizer::ATTRIBUTES =>['id','title','description','picture','author'=>['id','name'],'category'=>['id','name'],'createdAt']]);
        }
        return $form;      
    }

    //Update Book
    #[Rest\Patch(path:'/book/update/{id}',name: 'app_book_update')]
    #[Rest\View(serializerGroups:['book'], serializerEnableMaxDepthChecks:true)]

    public function updateBook(Book $book = null, Request $request)
    {
        if($book === null){
            $error = ['error'=>true,'message'=>'The book is not found.'];
            return $this->json($error,Response::HTTP_NOT_FOUND);
        }

        $bookDto = new BookDto();
        $form = $this->createForm(BookType::class,$bookDto,['method'=>$request->getMethod()]);
        $form->handleRequest($request);

        if(!$form->isSubmitted()){
            return new Response('Empty data', Response::HTTP_BAD_REQUEST);
        }

        if($bookDto->title){
            $book->setTitle($bookDto->title);
        }
        if($bookDto->description){
            $book->setDescription($bookDto->description);
        }
        if($bookDto->author){
            $author = $this->authorRepository->find($bookDto->author);
            if(!$author){
                $error = ['error'=>true,'message'=>'The author is not found.'];
                return $this->json($error,Response::HTTP_NOT_FOUND);
            }
            $book->setAuthor($author);
        }

        if($bookDto->category){
            $category = $this->categoryRepository->find($bookDto->category);
            if(!$category){
                $error = ['error'=>true,'message'=>'The category is not found.'];
                return $this->json($error,Response::HTTP_NOT_FOUND);
            }
            $book->setCategory($category);
        }
    
        if($bookDto->price){
            $book->setPrice($bookDto->price);
        }

        if($bookDto->picture){
            $book->setPicture($bookDto->picture);
        }

        $book->setCreatedAt(new \DateTime('now'));
        $this->em->persist($book);
        $this->em->flush();

        return $this->json($book,Response::HTTP_OK,[],[AbstractNormalizer::ATTRIBUTES=>['id','title','description','picture','price','author'=>['id','name'],'category'=>['id','name'],'createdAt']]);
    
    }
}
