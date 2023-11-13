<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;

class UserController extends AbstractFOSRestController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    // create user
    #[Rest\Post('/user/register', name: 'app_user_register')]
    #[View(serializerGroups:['user'], serializerEnableMaxDepthChecks: true)]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $user = new User();
        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($request);

        if(!$form->isSubmitted()){
            return $this->json(['code'=>400,'message'=>'You must send username and password'],Response::HTTP_BAD_REQUEST);
        }
        
        if($form->isValid()){
            $user->setCreatedAt(new \DateTimeImmutable('now'));
            $user->setRoles(['ROLE_USER']);
            // hash password
            
            $hashPassword = $userPasswordHasherInterface->hashPassword($user,$user->getPassword());
            $user->setPassword($hashPassword);

            //save data
            $this->em->persist($user);
            $this->em->flush();

            return $user;
        }else{
            return $form;
        }

    }
}
