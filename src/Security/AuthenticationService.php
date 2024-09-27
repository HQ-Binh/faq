<?php
// src/Security/AuthenticationService.php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class AuthenticationService
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function authenticate(Request $request): ?JsonResponse
    {
         if (!$request->getSession()->isStarted()) {
        $request->getSession()->start();
    }

        // Kiểm tra sessionId từ cookie
        $sessionId = $request->cookies->get('MYSESSIONID'); // Tên cookie bạn đã cấu hình
        if (!$sessionId) {
            return new JsonResponse(['error' => 'Cookie not found'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Kiểm tra sessionId từ session
        if ($sessionId && $request->getSession()->getId() !== $sessionId) {
            return new JsonResponse(['error' => 'Invalid session ID'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return null; // Không có lỗi
    }
}
