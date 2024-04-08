<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class RegistrationService
{
    private $entityManager;
    private $encodePassword;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $encodePassword)
    {
        $this->entityManager = $entityManager;
        $this->encodePassword = $encodePassword;
    }

    public function register( array $userData): array
    {

        if (empty($userData['username'] && $userData['password']))
        {
            throw new InvalidArgumentException('username and password cannot be empty');
        }

        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>$userData['username']]);

        if ($existingUser)
        {
            throw new ConflictHttpException('user already exist');
        }

        $user = new User();
        $user->setUsername($userData['username']);


        $qb = $this->entityManager->createQueryBuilder();
        $users = $qb->select('u')
            ->from(User::class, 'u')
            ->getQuery()
            ->getResult();

        $isadmin = false;
        foreach ($users as $userObj)
        {
            $roles = $userObj->getRoles();
            if (in_array('ROLE_ADMIN', $roles, true)) {
                $isadmin = true;
        }
        }

        if($isadmin)
        {
            $user->setRoles(['ROLE_USER']);
        }

        $wrongPassword = $userData['password'];

        if (strlen((string) $wrongPassword ) <= 5 || !preg_match("/^(?=.*[\W_]).+$/",$wrongPassword))
        {
            throw new InvalidArgumentException('must be at least 5 characters and one special character !!');
        }


        $encodePassword = $this->encodePassword->hashPassword($user,$userData['password']);
        $user->setPassword($encodePassword);


        $this->entityManager->persist($user);
        $this->entityManager->flush();


        return [
            'id'=>$user->getId(),
            'username'=>$user->getUsername(),
            'roles'=>$user->getRoles()
        ];

    }

}
