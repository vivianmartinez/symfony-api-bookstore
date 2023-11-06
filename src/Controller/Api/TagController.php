<?php

namespace App\Controller\Api;

use App\Entity\Tag;
use App\Form\Model\TagDto;
use App\Form\TagType;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class TagController extends AbstractFOSRestController
{
    private $em;
    private $tagRepository;
    
    public function __construct(EntityManagerInterface $em, TagRepository $tagRepository)
    {
        $this->em = $em;
        $this->tagRepository = $tagRepository;
    }

    //get all tags
    #[Rest\Get('/tags', name: 'app_tags')]
    #[Rest\View(serializerGroups:['tag'], serializerEnableMaxDepthChecks: true)]
    public function getAll()
    {
        $tags = $this->tagRepository->findAll();
        $view = $this->view($tags, Response::HTTP_OK);
        return $this->handleView($view);
        //return $this->json($tags,Response::HTTP_OK,['groups'=>'tag']);
    }

    //get single tag
    #[Rest\Get('/tag/{id}', name: 'app_tag')]
    #[Rest\View(serializerGroups:['tag'], serializerEnableMaxDepthChecks: true)]
    public function getSingle(Tag $tag = null): JsonResponse
    {
        if($tag == null){
            $error = ['error'=>true,'message'=>'The tag is not found.'];
            return $this->json($error,Response::HTTP_NOT_FOUND);
        }
        $view = $this->view($tag, Response::HTTP_OK);
        return $this->handleView($view);
    }

    //update tag
    #[Rest\Post('/tag/create', name: 'app_create_tag')]
    #[Rest\View(serializerGroups:['tag'], serializerEnableMaxDepthChecks: true)]
    public function createTag(Request $request)
    {
        $tagDto = new TagDto();
        $form = $this->createForm(TagType::class,$tagDto,['method'=>$request->getMethod()]);
        $form->handleRequest($request);

        if(!$form->isSubmitted())
        {
            $error = ['error'=>true,'message'=>'Invalid data.'];
            return $this->json($error,Response::HTTP_NOT_FOUND);
        }

        if(!$form->isValid()){
            return $form;
        }

        $tag = new Tag();
        $tag->setName($tagDto->name);
        
        $this->em->persist($tag);
        $this->em->flush();

        $view = $this->view($tag, Response::HTTP_OK);
        return $this->handleView($view);


    }

    //update tag
    #[Rest\Patch('/tag/{id}', name: 'app_update_tag')]
    #[Rest\View(serializerGroups:['tag'], serializerEnableMaxDepthChecks: true)]
    public function updateTag(Tag $tag = null, Request $request)
    {
        if($tag == null){
            $error = ['error'=>true,'message'=>'The tag is not found.'];
            return $this->json($error,Response::HTTP_NOT_FOUND);
        }

        $tagDto = new TagDto();
        $form = $this->createForm(TagType::class,$tagDto,['method'=>$request->getMethod()]);
        $form->handleRequest($request);

        if(!$form->isSubmitted()){
            $error = ['error' => true, 'message'=>'Invalid data'];
            return $this->json($error,Response::HTTP_BAD_REQUEST);
        }

        if(!$form->isValid()){
            return $form;
        }
        $tag->setName($tagDto->name);
        
        $this->em->persist($tag);
        $this->em->flush();

        $view = $this->view($tag, Response::HTTP_OK);
        return $this->handleView($view);
    }

}
