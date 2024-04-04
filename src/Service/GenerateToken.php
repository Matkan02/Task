<?php

namespace App\Service;


use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

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
        }catch (\Exception $e)
        {
            throw new \Exception('Failed to generate JWT token: ' . $e->getMessage());
        }

        return $token;
    }

}
