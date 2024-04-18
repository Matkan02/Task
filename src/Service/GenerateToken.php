<?php
declare(strict_types=1);

namespace App\Service;


use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class GenerateToken
{
    private $jwtManager;

    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    public function generateToken($user): string
    {
        try {
            $token = $this->jwtManager->create($user);
        }catch (AuthenticationException $e)
        {
            throw new AuthenticationException('Failed to generate token: '.$e->getMessage());
        }
        return $token;
    }

}
