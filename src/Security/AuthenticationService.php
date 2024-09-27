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

    public function authenticate(Request $request,&$user = null): ?JsonResponse
    {
         if (!$request->getSession()->isStarted()) {
             $request->getSession()->start();

        }

        // Kiểm tra sessionId từ cookie
        $sessionId = $request->cookies->get('MYSESSIONID'); // Tên cookie đã cấu hình
        $seasion=$request->getSession()->getMetadataBag()->getLastUsed();
        var_dump($seasion);
        if (!$sessionId) {
            return new JsonResponse(['error' => 'Cookie not found'], JsonResponse::HTTP_UNAUTHORIZED);
        }
        $encodedUserData = $request->cookies->get('USERDATA');
        // Lấy thông tin người dùng từ session
        // $user = $request->getSession()->getName(); 
    if ($encodedUserData) {
        // Giải mã dữ liệu từ base64
        $user = json_decode(base64_decode($encodedUserData), true);
        $user['last_login'] = new \DateTime();
        var_dump($user); // Trả về thông tin người dùng
    }

        // Kiểm tra sessionId từ session
        if ($sessionId && $request->getSession()->getId() !== $sessionId) {
            return new JsonResponse(['error' => 'Invalid session ID'], JsonResponse::HTTP_UNAUTHORIZED);
        }
        
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], JsonResponse::HTTP_UNAUTHORIZED);
        }
        var_dump($user);

        return null;
    }
}
