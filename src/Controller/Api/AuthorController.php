<?php

namespace App\Controller\Api;

use App\Entity\Author;
use App\Form\Model\AuthorDto;
use App\Form\AuthorType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;


class AuthorController extends AbstractFOSRestController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    //get all authors
    #[Rest\Get('/authors', name: 'app_authors')]
    #[Rest\View(serializerGroups:['author'],serializerEnableMaxDepthChecks:true)]
    public function getAll(): JsonResponse
    {
        $authors = $this->em->getRepository(Author::class)->findAll();
        $context = [AbstractNormalizer::ATTRIBUTES => ['id','name','books'=>['id','title','description','picture','price','category'=>['id','name']]]];
        return $this->json($authors,Response::HTTP_OK,[],$context);
    }

    //get single author
    #[Rest\Get('/author/{id}', name: 'app_single_author')]
    #[Rest\View(serializerGroups:['author'],serializerEnableMaxDepthChecks:true)]
    public function getSingle(Author $author = null): JsonResponse
    {
        if($author == null){
            $error = ['error'=>true,'message'=>'The author is not found.'];
            return $this->json($error,Response::HTTP_NOT_FOUND);
        }
        $context = [AbstractNormalizer::ATTRIBUTES => ['id','name','books'=>['id','title','description','picture','price','category'=>['id','name']]]];
        return $this->json($author,Response::HTTP_OK,[],$context);
    }

    // create an author
    #[Rest\Post(path:'/author/create', name:'app_author_create')]
    #[Rest\View(serializerGroups:['author'], serializerEnableMaxDepthChecks:true)]
    public function createAuthor(Request $request)
    {
        $authorDto = new AuthorDto();
        $form = $this->createForm(AuthorType::class,$authorDto);
        $form->handleRequest($request);

        if(!$form->isSubmitted()){
            $error = ['error' => true, 'message'=>'Invalid data'];
            return $this->json($error,Response::HTTP_BAD_REQUEST);
        }

        if(!$form->isValid()){
            return $form;
        }
        $author = new Author();
        $author->setName($authorDto->name);
        $this->em->persist($author);
        $this->em->flush();

        return $author;
    }

    //update author
    #[Rest\Patch(path:'/author/update/{id}',name:'app_author_update')]
    #[Rest\View(serializerGroups:['author'],serializerEnableMaxDepthChecks:true)]
    public function updateAuthor(Author $author = null,Request $request)
    {
        if($author == null){
            $error = ['error'=>'true','message'=>'Author not found'];
            return $this->json($error,Response::HTTP_NOT_FOUND);
        }
        $authorDto = new AuthorDto();
        $form = $this->createForm(AuthorType::class,$authorDto,['method'=>$request->getMethod()]);
        $form->handleRequest($request);

        if(!$form->isSubmitted()){
            $error = ['error' => true, 'message'=>'Invalid data'];
            return $this->json($error,Response::HTTP_BAD_REQUEST);
        }

        if(!$form->isValid()){
            return $form;
        }

        $author->setName($authorDto->name);
        $this->em->persist($author);
        $this->em->flush();

        return $author;
    }

    //delete author
    #[Rest\Delete(path:'/author/delete/{id}', name:'app_author_delete')]
    #[Rest\View(serializerGroups:['author'],serializerEnableMaxDepthChecks:true)]
    public function deleteAuthor(Author $author = null):JsonResponse
    {
        if($author == null){
            $error = ['error'=>'true','message'=>'Author not found'];
            return $this->json($error,Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($author);
        $this->em->flush();

        $result = ['error'=>false,'message'=>'Author deleted succesfully.'];
        return $this->json($result,Response::HTTP_OK);
    }

}
