<?php

namespace App\Controller\Api;

use App\Entity\Book;
use App\Entity\Tag;
use App\Form\BookType;
use App\Form\Model\BookDto;
use App\Form\Model\TagDto;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use App\Repository\TagRepository;
use App\Service\UploadFile;
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
    private $tagRepository;
    private $uploadFile;
    private $context;

    public function __construct(
        EntityManagerInterface $em, 
        AuthorRepository $authorRepository, 
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository, 
        UploadFile $uploadFile )
    {
        $this->em = $em;
        $this->authorRepository   = $authorRepository;
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository      = $tagRepository;
        $this->uploadFile         = $uploadFile;
        $this->context = [AbstractNormalizer::ATTRIBUTES => ['id', 'title','description','price','picture','author'=>['id','name'],'category'=>['id','name'],'tags'=>['id','name']]];
    }

    //Route list all books

    #[Rest\Get(path:'/books', name: 'app_books')]
    #[Rest\View(serializerGroups:['book'],serializerEnableMaxDepthChecks:true)]
    public function index(BookRepository $bookRepository): JsonResponse
    {
        $books = $bookRepository->findAll();
        return $this->json($books,Response::HTTP_OK,[],$this->context);
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
            //if send image
            if($bookDto->imageBase64){
                $path = $this->uploadFile->uploadImageBase64($bookDto->imageBase64);
                $book->setPicture($path);
            }

            $book->setPrice($bookDto->price);
            $author   = $this->authorRepository->find($bookDto->author);
            $category = $this->categoryRepository->find($bookDto->category);
            $book->setAuthor($author);
            $book->setCategory($category);
            $book->setCreatedAt(new \DateTime('now'));
            $this->em->persist($book);
            $this->em->flush();
    
            return $this->json($book,Response::HTTP_OK,[],$this->context);
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

        if($bookDto->imageBase64){
            $path = $this->uploadFile->uploadImageBase64($bookDto->imageBase64);
            $book->setPicture($path);
        }

        // if send tags
        if($bookDto->tags){
            //get all current tags in this book
            $current_tags_book = [];
            foreach($book->getTags() as $bookTag){
                array_push($current_tags_book,$bookTag->getId());
            }

            foreach($bookDto->tags as $tagDto){
                //validate if send correct data
                if($tagDto->id == null && $tagDto->name == null){
                    $error = ['error'=>true,'message'=>'You must enter the tag id to add a tag to book or send name to create a new tag'];
                    return $this->json($error,Response::HTTP_FORBIDDEN);
                }else if($tagDto->id !== null && $tagDto->name !== null){
                    $error = ['error'=>true,'message'=>'Bad request. If tag exist send id or create a new tag sending only name'];
                    return $this->json($error,Response::HTTP_FORBIDDEN);
                }
                $addTag = null;
                // if send id verify if exists on Tag
                if($tagDto->id){
                    $tag = $this->tagRepository->find($tagDto->id);
                    if(!$tag){
                        $error = ['error'=>true,'message'=>'Bad request. The tag not exists. Verify Id or create a new tag.'];
                        return $this->json($error,Response::HTTP_FORBIDDEN);
                    }elseif(!in_array($tagDto->id,$current_tags_book)){
                        //if id exists on Tag and not exists in book->getTags() we add the tag to the book
                        $addTag = $tag;
                        /*
                        $error = ['error'=>true,'message'=>'Bad request. The tag even exists on this book.'];
                        return $this->json($error,Response::HTTP_FORBIDDEN);
                        */
                    }
                    
                }elseif($tagDto->name){   
                    //verify if send name and that name doesn't exists on Tag
                    $tag = $this->tagRepository->findOneByName($tagDto->name);
                    //if name even exists return error
                    if($tag){
                        $error = ['error'=>true,'message'=>'The tag even exists on Tags. Assign it to your book sending id '. $tag['id']];
                        return $this->json($error,Response::HTTP_FORBIDDEN);
                    }
                    //if there is no error we create the tag
                    $newTag = new Tag();
                    $newTag->setName($tagDto->name);
                    $this->em->persist($newTag);
                    $this->em->flush($newTag);
                    $addTag = $newTag;
                }
                // if there is value on addTag add tag to book
                if($addTag !== null){
                    $book->addTag($addTag);
                }
            }
        }

        $this->em->persist($book);
        $this->em->flush($book);

        return $this->json($book,Response::HTTP_OK,[],$this->context);
    
    }

    //delete book
    #[Rest\Delete(path:'/book/delete/{id}',name:'app_book_delete')]
    #[Rest\View(serializerGroups:['book'],serializerEnableMaxDepthChecks:true)]
    public function deleteBook(Book $book = null)
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
    public function deleteTagBook(Book $book = null,Request $request)
    {
        if($book == null){
            $error = ['error'=>true,'message'=>'The book is not found.'];
            return $this->json($error,Response::HTTP_NOT_FOUND);
        }

        $bookDto = new BookDto();
        $form = $this->createForm(BookType::class, $bookDto,['method' => $request->getMethod()]);
        $form->handleRequest($request);
        if(!$form->isSubmitted()){
            return new Response('Empty data', Response::HTTP_BAD_REQUEST);
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
