<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\GenerateToken;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Flex\Response;

class UserLoginController extends AbstractController
{
    private  $generateToken;
    private $tokenStorage;

    public function __construct(GenerateToken $generateToken,TokenStorageInterface $tokenStorage)
    {
     $this->generateToken = $generateToken;
     $this->tokenStorage = $tokenStorage;
    }

    #[Route('/user/login', name: 'user_login', methods: 'POST')]
    public function loginUser(#[CurrentUser] ?User $user): JsonResponse
    {
        if($user === null)
        {
            return $this->json(['error'=>'Not Authorized'],JsonResponse::HTTP_UNAUTHORIZED);
        }

        $token = $this->generateToken->generateToken($user);


        $response = $this->json([]);
        $response->headers->set('Authorization', 'Bearer '.$token);

        return $response;
    }

    #[Route('/user/logout', name: 'user_logout', methods: 'POST')]
    public function logoutUser(#[CurrentUser] ?User $user):JsonResponse
    {
        $userToken = $this->tokenStorage->getToken();

        if ($userToken->getUser() === $user)
        {
            $this->tokenStorage->setToken(null);
            return $this->json(['message'=>'Logout successfull !!']);
        }
        return $this->json(['message' => 'User is not logged'], JsonResponse::HTTP_UNAUTHORIZED);
    }

}
