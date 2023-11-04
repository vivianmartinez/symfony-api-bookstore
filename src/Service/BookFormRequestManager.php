<?php


namespace App\Service;

use App\Entity\Book;
use App\Form\BookType;
use App\Form\Model\BookDto;
use App\Repository\AuthorRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\Switch_;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



class BookFormRequestManager{

    private $em;
    private $authorRepository;
    private $categoryRepository;
    private $uploadFile;
    private $addTags;
    private $formFactoryInterface;


    public function __construct(
        EntityManagerInterface $em,
        AuthorRepository $authorRepository,
        CategoryRepository $categoryRepository,
        UploadFile $uploadFile,
        AddTags $addTags,
        FormFactoryInterface $formFactoryInterface
    )
    {
        $this->em                 = $em;
        $this->authorRepository   = $authorRepository;
        $this->categoryRepository = $categoryRepository;
        $this->uploadFile         = $uploadFile;
        $this->addTags            = $addTags;
        $this->formFactoryInterface = $formFactoryInterface;
    }


    public function __invoke(Book $book, Request $request)
    {
        switch ($request->getMethod()){
            case 'POST':
                return $this->postBookAction($book,$request);
                break;
            case 'PATCH':
                return $this->patchBookAction($book,$request);
                break;
        }
    }


    public function postBookAction(Book $book,Request $request)
    {
        $bookDto = new BookDto();
        $form = $this->formFactoryInterface->create(BookType::class,$bookDto);
        $form->handleRequest($request);
        if(!$form->isSubmitted()){
            $error = ['error'=> true,'message'=>'Empty data.'];
            return [$error, Response::HTTP_BAD_REQUEST];
        }

        if($form->isValid())
        {
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
            //if author doesn't exists return error
            if(!$author){
                $error = ['error'=>true,'message'=>'The author is not found.'];
                return [$error, Response::HTTP_NOT_FOUND];
            }
            $book->setAuthor($author);

            //if category doesn't exists return error
            if(!$category){
                $error = ['error'=>true,'message'=>'The category is not found.'];
                return [$error, Response::HTTP_NOT_FOUND];
            }
            $book->setCategory($category);
            if($bookDto->tags){
                foreach($bookDto->tags as $tagDto){
                    //use custom service addTags 
                    $tag_add = $this->addTags->addTagsBook([],$tagDto);
    
                    if($tag_add['error'] !== null){
                        $error = $tag_add['error'];
                        return [$error, Response::HTTP_BAD_REQUEST];
                    }
                    // if there is value on addTag add tag to book
                    if($tag_add['add_tag'] !== null){
                        $book->addTag($tag_add['add_tag']);
                    }
                }
            }
            $book->setCreatedAt(new \DateTime('now'));
            $this->em->persist($book);
            $this->em->flush();
    
            return [$book,Response::HTTP_OK];
        }
        return [$form,Response::HTTP_BAD_REQUEST];
    }

    public function patchBookAction(Book $book, Request $request)
    {

        $bookDto = new BookDto();
        $form = $this->formFactoryInterface->create(BookType::class,$bookDto,['method'=>$request->getMethod()]);
        $form->handleRequest($request);

        if(!$form->isSubmitted()){
            $error = ['error'=> true,'message'=>'Empty data.'];
            return [$error, Response::HTTP_BAD_REQUEST];
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
                return [$error, Response::HTTP_NOT_FOUND];
            }
            $book->setAuthor($author);
        }
        if($bookDto->category){
            $category = $this->categoryRepository->find($bookDto->category);
            if(!$category){
                $error = ['error'=>true,'message'=>'The category is not found.'];
                return [$error, Response::HTTP_NOT_FOUND];
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
            foreach($bookDto->tags as $tagDto){
                //use custom service addTags 
                $tag_add = $this->addTags->addTagsBook($book->getTags(),$tagDto);

                if($tag_add['error'] !== null){
                    $error = $tag_add['error'];
                    return [$error, Response::HTTP_BAD_REQUEST];
                }

                // if there is value on addTag add tag to book
                if($tag_add['add_tag'] !== null){
                    $book->addTag($tag_add['add_tag']);
                }
            }
        }    
        $this->em->persist($book);
        $this->em->flush($book);
        return [$book,Response::HTTP_OK];
        
    }
}