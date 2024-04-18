<?php
declare(strict_types=1);

namespace App\Service;

use App\BadReq;
use App\Entity\Tasks;
use App\EmptyException;
use App\Entity\User;
use App\NoneId;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class TaskService
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function createTask($taskData)
    {


        if (empty($taskData['title']))
        {

            throw new InvalidArgumentException('Title cannot be empty');
        }

        $existingTask = $this->entityManager->getRepository(Tasks::class)->findOneBy(['title' => $taskData['title']]);

        if ($existingTask !== null)
        {

            throw new ConflictHttpException('Task with the name already exists');
        }

        $task = new Tasks();

        $task->setTitle($taskData['title']);
        $task->setDescription($taskData['description']);
        $task->setCreatedat(new \DateTime());
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $task;

    }

    public function viewAllTask(#[CurrentUser] ?User $user ): array
    {


        if (in_array('ROLE_ADMIN',$user->getRoles(),true))
        {
            $taskData = $this->entityManager->getRepository(Tasks::class)->findAll();

        }else
        {
            $taskData = $user->getTasksUser();
        }


        $tasks = [];
        if (count($taskData) > 0)
        {
            foreach ($taskData as $task)
            {
                $tasksTab = [
                    'title'=>$task->getTitle(),
                    'description'=>$task->getDescription()
                ];

                if (!in_array('ROLE_ADMIN',$user->getRoles(),true))
                {
                    $countAssigned = $user->getTasksUser()->count();
                    $tasksTab['assigned-tasks'] = $countAssigned;
                }
                $tasks[]= $tasksTab;
            }
        }
        else
        {
            $tasks [] = [
                'title'=>'',
                'description'=>''
            ];
        }

        return $tasks;

    }

    public function viewTaskById(int $taskId): array
    {

      $tasksBYId = $this->entityManager->getRepository(Tasks::class)->find($taskId);
      if ($tasksBYId === null)
      {
          return ['error'=>'Tasks not found'];
      }

       $tasks = [
           'title'=>$tasksBYId->getTitle() ,
           'description'=>$tasksBYId->getDescription()
       ];
        return $tasks;
    }

    public function editTaskById($taskId, $title, $description): void
    {
        $task = $this->entityManager->getRepository(Tasks::class)->find($taskId);

        if(!$task)
        {
            throw new EmptyException('task not found');
        }

        if (empty($title)) {
            throw new NoneId('Title cannot be empty');
        }

        $alreadyExistTask = $this->entityManager->getRepository(Tasks::class)->findOneBy(['title'=>$title]);
        if ($alreadyExistTask !== null)
        {
            throw new ConflictHttpException('Title already Exist');
        }

        $task->setTitle($title);
        $task->setDescription($description);

        $this->entityManager->flush();


    }

    public function deleteTask(int $taskId): void
    {
        $task = $this->entityManager->getRepository(Tasks::class)->find($taskId);

        if (!$task)
        {
            throw new EmptyException('task not found');
        }

        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }

    public function assignedTaskTo(int $userId, int $taskId): void
    {
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        $task = $this->entityManager->getRepository(Tasks::class)->find($taskId);

        if (!$task || !$user)
        {
            throw new EmptyException('Task or user not found');
        }

        $task->setUserTask($user);

        $this->entityManager->flush();
    }


}
