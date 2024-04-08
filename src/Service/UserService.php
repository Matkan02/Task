<?php
declare(strict_types=1);

namespace App\Service;

use App\EmptyException;
use App\Entity\User;
use App\NoneId;
use App\BadReq;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    private $entityManager;
    private $encodePassword;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $encodePassword)
    {
        $this->entityManager = $entityManager;
        $this->encodePassword = $encodePassword;
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

    public function editUser(int $userId, $newPassword): void
    {
        $userEdit = $this->entityManager->getRepository(User::class)->find($userId);

        if(empty($newPassword))
        {
            throw new NoneId('Password cannot be empty');
        }

        if (strlen((string)$newPassword ) <= 5 || !preg_match("/^(?=.*[\W_]).+$/",$newPassword))
        {
            throw new InvalidArgumentException('Password must be at least 5 characters and one special character !!');
        }


        if ($this->encodePassword->isPasswordValid($userEdit, $newPassword))
        {
            throw new BadReq('This is the current password');
        }

        $encodePassword = $this->encodePassword->hashPassword($userEdit,$newPassword);


        $userEdit->setPassword($encodePassword);

        $this->entityManager->flush();

    }



}
