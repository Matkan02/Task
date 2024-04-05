<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\GenerateToken;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Flex\Response;

class UserLoginController extends AbstractController
{
    private  $generateToken;

    public function __construct(GenerateToken $generateToken)
    {
     $this->generateToken = $generateToken;
    }

    #[Route('/user/login', name: 'app_user_login', methods: 'POST')]
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
}
