<?php

namespace App\Controller\Api;

use App\Entity\Book;
use App\Form\BookType;
use App\Form\Model\BookDto;
use App\Repository\BookRepository;
use App\Repository\TagRepository;
use App\Service\BookFormRequestManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;


class BookController extends AbstractFOSRestController
{
    private $em;
    private $tagRepository;
    private $bookFormRequestManager;
    private $context;

    public function __construct(
        EntityManagerInterface $em, 
        TagRepository $tagRepository, 
        BookFormRequestManager $bookFormRequestManager    
    )
    {
        $this->em = $em;
        $this->tagRepository = $tagRepository;
        $this->bookFormRequestManager = $bookFormRequestManager;
        $this->context = [AbstractNormalizer::ATTRIBUTES => ['id', 'title','description','price','picture','author'=>['id','name'],'category'=>['id','name'],'tags'=>['id','name']]];
    }

    //Route list all books

    #[Rest\Get(path:'/books', name: 'app_books')]
    #[Rest\View(serializerGroups:['book'],serializerEnableMaxDepthChecks:true)]
    public function getAll(BookRepository $bookRepository): JsonResponse
    {
        $books = $bookRepository->findAll();
        return $this->json($books,Response::HTTP_OK,[],$this->context);
    }

    //Route get single book

    #[Rest\Get(path:'/book/{id}', name: 'app_single_book')]
    #[Rest\View(serializerGroups:['book'],serializerEnableMaxDepthChecks:true)]
    public function getSingle(Book $book = null): JsonResponse
    {
        if($book == null){
            $error = ['error'=>true,'message'=>'The book is not found.'];
            return $this->json($error,Response::HTTP_NOT_FOUND);
        }
        return $this->json($book,Response::HTTP_OK,[],$this->context);
    }

    //Create book
    #[Rest\Post(path:'/book/create',name:'app_book_create')]
    #[Rest\View(serializerGroups:['book'],serializerEnableMaxDepthChecks:true)]
    public function createBook(Request $request): JsonResponse
    {
        $book = new Book();
        //use our service App\Service\BookFormRequestManager to create form and return the response

        [$book,$response_status] = ($this->bookFormRequestManager)($book,$request);
        return $this->json($book,$response_status,[],$this->context);
    }

    //Update Book
    #[Rest\Patch(path:'/book/update/{id}',name: 'app_book_update')]
    #[Rest\View(serializerGroups:['book'], serializerEnableMaxDepthChecks:true)]

    public function updateBook(Book $book = null, Request $request): JsonResponse
    {
        if($book == null){
            $error = ['error'=>true,'message'=>'The book is not found.'];
            return $this->json($error,Response::HTTP_NOT_FOUND);
        }
        //use our service App\Service\BookFormRequestManager to create form and return the response
        [$book,$response_status] = ($this->bookFormRequestManager)($book,$request);

        return $this->json($book,$response_status,[],$this->context);    
    }

    //delete book
    #[Rest\Delete(path:'/book/delete/{id}',name:'app_book_delete')]
    #[Rest\View(serializerGroups:['book'],serializerEnableMaxDepthChecks:true)]
    public function deleteBook(Book $book = null): JsonResponse
    {
        if($book == null){
            $error = ['error'=>true,'message'=>'The book is not found.'];
            return $this->json($error,Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($book);
        $this->em->flush();
        $result = ['error'=>false,'message'=>'Book deleted succesfully.'];
        return $this->json($result,Response::HTTP_OK);
    }

    //delete tags of book
    #[Rest\Patch(path:'/book/{id}/delete/tags', name: 'app_delete_tags_book', requirements:['id'=>'\d+'])]
    #[Rest\View(serializerGroups:['book'], serializerEnableMaxDepthChecks:true)]
    public function deleteTagBook(Book $book = null,Request $request): JsonResponse
    {
        if($book == null){
            $error = ['error'=>true,'message'=>'The book is not found.'];
            return $this->json($error,Response::HTTP_NOT_FOUND);
        }

        $bookDto = new BookDto();
        $form = $this->createForm(BookType::class, $bookDto,['method' => $request->getMethod()]);
        $form->handleRequest($request);
        if(!$form->isSubmitted()){
            $error = ['error' => true, 'message'=>'Invalid data'];
            return $this->json($error,Response::HTTP_BAD_REQUEST);
        }

        if($bookDto->tags){
            $current_tags_book = [];
            foreach($book->getTags() as $bookTag){
                array_push($current_tags_book,$bookTag->getId());
            }

            foreach($bookDto->tags as $tagDto){
                if(!$tagDto->id){
                    $error = ['error'=>true,'message'=>'Bad request. You must specify the tag id.'];
                    return $this->json($error,Response::HTTP_FORBIDDEN);
                }else if(!in_array($tagDto->id,$current_tags_book)){
                    $error = ['error'=>true,'message'=>'Bad request. The tag not exists in this book.'];
                    return $this->json($error,Response::HTTP_FORBIDDEN);
                }
                $book->removeTag($this->tagRepository->find($tagDto->id));
                $this->em->persist($book);
                $this->em->flush();
                $this->em->refresh($book);
            }
        }
        return $this->json($book,Response::HTTP_OK,[],$this->context);
    }
}
