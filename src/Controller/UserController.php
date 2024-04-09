<?php

namespace App\Controller;

use App\BadReq;
use App\CannotChangePassword;
use App\EmptyException;
use App\NoneId;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
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

    #[Route('/user/edit/{id}', name: 'edit_user',methods:['PUT'])]
    public function editUserById(int $id, Request $request): JsonResponse
    {

        $editData = json_decode($request->getContent(),true);

        $currentUser = $this->getUser();
        $password = $editData['password'];
        try {
            $this->userService->editUser($id,$password,$currentUser);
            return $this->json(['message'=>"Successfull"],Response::HTTP_OK);
        }catch (NoneId $e)
        {
            return $this->json(['error'=>$e->getMessage()],Response::HTTP_NOT_FOUND);
        }catch (InvalidArgumentException $e)
        {
            return $this->json(['error'=>$e->getMessage()],Response::HTTP_BAD_REQUEST);
        }catch (BadReq $e)
        {
            return $this->json(['error'=>$e->getMessage()],Response::HTTP_CONFLICT);
        }catch (CannotChangePassword $e)
        {
            return $this->json(['error'=>$e->getMessage()],Response::HTTP_FORBIDDEN);
        }


    }

}
