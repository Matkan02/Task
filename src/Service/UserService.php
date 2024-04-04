<?php

namespace App\Service;

use App\EmptyException;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function deleteUser(int $userId): void
    {
        $userDelete = $this->entityManager->getRepository(User::class)->find($userId);

        if (!$userDelete)
        {
            throw new EmptyException('User not found');
        }

        $this->entityManager->remove($userDelete);
        $this->entityManager->flush();
    }



}
