<?php

// src/Security/ApiKeyAuthenticator.php
namespace App\Security;

use App\Repository\ApiTokenRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    private $userRepository;
    private $apiTokenRepository;

    public function __construct(UserRepository $userRepository,ApiTokenRepository $apiTokenRepository)
    {
        $this->userRepository     = $userRepository;
        $this->apiTokenRepository = $apiTokenRepository;
    }
    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        
        $route = $request->attributes->get('_route');

        if(! $request->headers->has('X-AUTH-TOKEN')){
            if($route == 'app_api_login' || $route == 'app_user_register'){
                return false; // not need authenticate
            }
            throw new CustomUserMessageAuthenticationException('No Api token provided');
        }
        //if is true authenticate
        return true;

    }
    
    public function authenticate(Request $request): Passport
    {
        
        $apiToken = $request->headers->get('X-AUTH-TOKEN');
        if (null === $apiToken) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        // implement your own logic to get the user identifier from `$apiToken`
        // e.g. by looking up a user in the database using its API key
    
        return new SelfValidatingPassport(new UserBadge($apiToken, function($apiToken){

            $user = $this->userRepository->findByToken($apiToken);
            if(!$user){
                throw new UserNotFoundException();
            }
            $currend_date = new \DateTime('now');
            $expired = $this->apiTokenRepository->findByTokenExpired($currend_date,$apiToken);
        
            if(null === $expired){
                throw new CustomUserMessageAuthenticationException('The Token has expired');
            }
            return $user;
        }));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}