<?php

namespace App\Controller;

use App\EmptyException;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[Route('/user/delete/{id}', name: 'delete_user',methods:['DELETE'])]
    public function deleteUser(int $id): JsonResponse
    {

        try {
            $this->userService->deleteUser($id);
            return $this->json(['message'=>"Deleted.$id"],Response::HTTP_OK);
        }catch (EmptyException $e)
        {
            return $this->json(['error'=>$e->getMessage()],Response::HTTP_NOT_FOUND);
        }


    }

}
