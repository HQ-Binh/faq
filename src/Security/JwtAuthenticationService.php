<?php
// src/Security/JwtAuthenticationService.php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class JwtAuthenticationService
{
    private $jwtManager;
    private $tokenStorage;
    private $userProvider;
    private $jwtEncoder;

    public function __construct(JWTTokenManagerInterface $jwtManager, TokenStorageInterface $tokenStorage, UserProviderInterface $userProvider, JWTEncoderInterface $jwtEncoder)
    {
        $this->jwtManager = $jwtManager;
        $this->tokenStorage = $tokenStorage;
        $this->userProvider = $userProvider;
        $this->jwtEncoder = $jwtEncoder;
    }

    public function authenticate(Request $request): ?UserInterface
    {
        // Lấy token từ header Authorization
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            throw new AuthenticationException('No token provided');
        }

        $token = $matches[1];

        // Giải mã token
        try {
            $data = $this->jwtEncoder->decode($token);
        } catch (\Exception $e) {
            throw new AuthenticationException('Invalid token');
        }

        // Lấy thông tin người dùng từ dữ liệu token đã giải mã
        $identifier = $data['username'] ?? null;  // Hoặc 'email', tùy vào cấu trúc token

        if (!$identifier) {
            throw new AuthenticationException('Token does not contain a valid identifier');
        }

        // Tìm người dùng dựa trên identifier (username hoặc email)
        $user = $this->userProvider->loadUserByIdentifier($identifier);
        if (!$user) {
            throw new AuthenticationException('User not found');
        }
        $roles = $user->getRoles();
        if (!is_array($roles)) {
            $roles = [$roles]; 
        }
        // Tạo token và lưu vào TokenStorage
        $this->tokenStorage->setToken(new UsernamePasswordToken($user, 'main',$roles ));

        return $user;
    }
}
