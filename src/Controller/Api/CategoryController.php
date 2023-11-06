<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;


class CategoryController extends AbstractFOSRestController
{
    private $em;
    private $context;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->context = [AbstractNormalizer::ATTRIBUTES => ['id','name','books'=>['id','title','description','picture','price','author'=>['id','name']]]];
    }
    //list all categories
    #[Rest\Get('/categories', name: 'app_categories')]
    #[Rest\View(serializerGroups:['category'],serializerEnableMaxDepthChecks:true)]
    public function getAll(): JsonResponse
    {
        $categories = $this->em->getRepository(Category::class)->findAll();
        return $this->json($categories,Response::HTTP_OK,[],$this->context);
    }

    //get single category
    #[Rest\Get('/category/{id}', name: 'app_single_category')]
    #[Rest\View(serializerGroups:['category'],serializerEnableMaxDepthChecks:true)]
    public function getSingle(Category $category = null): JsonResponse
    {
        if($category == null){
            $error = ['error'=>true,'message'=>'The category is not found.'];
            return $this->json($error,Response::HTTP_NOT_FOUND);
        }
        return $this->json($category,Response::HTTP_OK,[],$this->context);
    }

    // create category
    #[Rest\Post(path:'/category/create', name:'app_category_create')]
    #[Rest\View(serializerGroups:['category'],serializerEnableMaxDepthChecks:true)]
    public function createCategory(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class,$category);
        $form->handleRequest($request);

        if(!$form->isSubmitted()){
            $error = ['error' => true, 'message'=>'Invalid data'];
            return $this->json($error,Response::HTTP_BAD_REQUEST);
        }

        if(!$form->isValid()){
            return $form;
        }

        $this->em->persist($category);
        $this->em->flush();

        return $category;
    }

    //update category
    #[Rest\Patch(path:'/category/update/{id}',name:'app_category_update')]
    #[Rest\View(serializerGroups:['category'],serializerEnableMaxDepthChecks:true)]
    public function updateCategory(Category $category = null, Request $request)
    {
        if($category == null){
            $error = ['error'=>'true','message'=>'Category not found'];
            return $this->json($error,Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(CategoryType::class,$category,['method' => $request->getMethod()]);
        $form->handleRequest($request);

        if(!$form->isSubmitted()){
            $error = ['error' => true, 'message'=>'Invalid data'];
            return $this->json($error,Response::HTTP_BAD_REQUEST);
        }

        if(!$form->isValid()){
            return $form;
        }

        $this->em->persist($category);
        $this->em->flush();

        return $category;
    }

    //delete category
    #[Rest\Delete(path:'/category/delete/{id}',name:'app_category_delete')]
    #[Rest\View(serializerGroups:['category'],serializerEnableMaxDepthChecks:true)]
    public function deleteCategory(Category $category = null):JsonResponse
    {
        if($category == null){
            $error = ['error'=>'true','message'=>'Category not found'];
            return $this->json($error,Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($category);
        $this->em->flush();

        $result = ['error'=>false,'message'=>'Category deleted succesfully.'];
        return $this->json($result,Response::HTTP_OK);
    }
}
