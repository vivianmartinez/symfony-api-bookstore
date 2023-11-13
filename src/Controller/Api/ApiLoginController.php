<?php

namespace App\Controller\Api;

use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\CurrentUser;


class ApiLoginController extends AbstractFOSRestController
{
    private $em;
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->em = $entityManagerInterface;
    }

    #[Rest\Post('/login', name: 'app_api_login')]
    #[View(serializerGroups:['user'])]
    public function login(#[CurrentUser] ?User $user): JsonResponse
    {
        if($user === null){
            return $this->json([
                'message' => 'missing credentials'
            ],Response::HTTP_UNAUTHORIZED);
        }

        $apiToken = new ApiToken($user);
        $this->em->persist($apiToken);
        $this->em->flush();

        return $this->json([
            'user'  => $user->getUserIdentifier(),
            'token' => $apiToken->getToken()
        ]);
    }
}
