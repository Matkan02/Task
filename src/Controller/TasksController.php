<?php

namespace App\Controller;


use App\EmptyException;
use App\NoneId;
use App\Service\TaskService;
use Symfony\Component\Config\Definition\Exception\ForbiddenOverwriteException;
use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Attribute\Route;

class TasksController extends AbstractController
{
    private $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }
    #[Route('/task/addtask', name: 'add_task',methods:['POST'])]
    public function addTask(Request $request): JsonResponse
    {


        $data = json_decode($request->getContent(),true);

        if (!isset($data['title']))
        {
            return new JsonResponse(['error'=>'Invalid data'],Response::HTTP_BAD_REQUEST);
        }
        try {
            $this->taskService->createTask($data);
            return $this->json(['message'=>'Successfull!!!'],Response::HTTP_OK);
        }catch (InvalidArgumentException $e)
        {
            return $this->json(['error'=>$e->getMessage()],Response::HTTP_BAD_REQUEST);
        }catch (ConflictHttpException $e)
        {
            return $this->json(['error'=>$e->getMessage()],Response::HTTP_CONFLICT);
        }catch (ForbiddenOverwriteException $e)
        {
            return $this->json(['error'=>$e->getMessage()],Response::HTTP_FORBIDDEN);
        }

    }

    #[Route('/task/viewtask', name: 'getall_task',methods:['GET'])]
     public function viewTaskAll(): JsonResponse
    {
        $viewTask = $this->taskService->viewAllTask($this->getUser());
        return $this->json($viewTask);
    }

    #[Route('/task/{ids}', name: 'get_task',methods:['GET'])]
    public function viewTaskId(string $ids): JsonResponse
    {

        $idsArray = explode(',',$ids);
        $tasks = [];
        foreach ($idsArray as $id)
        {
           if (!is_numeric($id))
            {
                return $this->json(['error'=>'Invalid Id'],Response::HTTP_BAD_REQUEST);
            }
            $task = $this->taskService->viewTaskById((int)$id);

            if (isset($task['error']))
            {
                return $this->json($task,Response::HTTP_NOT_FOUND);
            }
                $tasks [] = $task;
        }
        return $this->json($tasks);
    }

    #[Route('/task/edit/{id}', name: 'get_task',methods:['PUT'])]
    public function editTaskId(int $id, Request $request): JsonResponse
    {
       $editData = json_decode($request->getContent(),true);


        if (!isset($editData['title']) || !isset($editData['description']))
        {
            return $this->json(['error' => 'Title and description are required'], Response::HTTP_BAD_REQUEST);
        }

       $title = $editData['title'] ;
       $description = $editData['description'] ;

        try {
            $this->taskService->editTaskById($id, $title, $description);
            return $this->json(['message' => 'Edit successfull'], Response::HTTP_OK);
        }
        catch (EmptyException $e)
        {
            return $this->json(['error'=>$e->getMessage()],Response::HTTP_NOT_FOUND);
        }
        catch (NoneId $e)
        {
            return $this->json(['error'=>$e->getMessage()],Response::HTTP_BAD_REQUEST);
        }catch (ConflictHttpException $e)
        {
            return $this->json(['error'=>$e->getMessage()],Response::HTTP_CONFLICT);
        }
    }

    #[Route('/task/delete/{id}', name: 'delete_task',methods:['DELETE'])]
    public function deleteTask(int $id): JsonResponse
    {
        try {
            $this->taskService->deleteTask($id);
            return $this->json(['message'=>'Successfull!!!'],Response::HTTP_OK);
        }catch (EmptyException $e)
        {
            return $this->json(['error'=>$e->getMessage()],Response::HTTP_NOT_FOUND);
        }

    }

    #[Route('/task/assign-task', name: 'assign_task',methods:['POST'])]
    public function assignTaskToUser(Request $request): JsonResponse
    {
        $assign = json_decode($request->getContent(),true);

        $userId = $assign['userId'] ?? null;
        $taskId = $assign['taskId'] ?? null;

        if (!isset($userId) || !isset($taskId))
        {
            return new JsonResponse(['error'=>'Invalid'],Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->taskService->assignedTaskTo($userId,$taskId);
            return $this->json(['message'=>'Assigned Successfull !!!'],Response::HTTP_OK);
        }catch (EmptyException $e)
        {
            return $this->json(['error'=>$e->getMessage()],Response::HTTP_NOT_FOUND);
        }

    }


}
