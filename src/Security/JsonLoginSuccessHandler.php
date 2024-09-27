<?php
// src/Security/JsonLoginSuccessHandler.php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class JsonLoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $logger;
    private $tokenStorage;

    public function __construct(LoggerInterface $logger,TokenStorageInterface $tokenStorage)
    {
        $this->logger = $logger; // Khởi tạo logger
        $this->tokenStorage = $tokenStorage;
    }
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        // Lấy thông tin người dùng từ token
        $user = $token->getUser();
        var_dump($user);
       
            // Khởi tạo một session mới
            if (!$request->getSession()->isStarted()) {
                $request->getSession()->start();
            }
            
            // Lấy session ID mới
            $sessionId = $request->getSession()->getId();

    // Mã hóa thông tin người dùng
    $userData = [
        'email' => $user->getUserIdentifier(),
        'roles' => $user->getRoles(),
        'sessionId' => $sessionId,
    ];

    // JSON encode và mã hóa base64 để lưu vào cookie
    $encodedUserData = base64_encode(json_encode($userData));

    // Đặt token vào TokenStorage
    $this->tokenStorage->setToken($token);
    

            var_dump($sessionId);
            $this->tokenStorage->setToken($token);
            $tokenResult = $this->tokenStorage->getToken();
            var_dump($tokenResult->getUser());
        
        // Tạo phản hồi JSON với thông tin người dùng (có thể tùy chỉnh)
         $response= new JsonResponse([
            'status' => 'User logged in successfully!',
            'sessionId' => $sessionId,
            'email' => $user->getUserIdentifier(),  // lấy email
            'roles' => $user->getRoles(),            // Lấy quyền của người dùng
        ], JsonResponse::HTTP_OK);
        // Đặt cookie chứa thông tin người dùng đã mã hóa
    $response->headers->setCookie(new Cookie('USERDATA', $encodedUserData, time() + 3600, '/', null, false, true));
    // $response->headers->setCookie(new Cookie('MYSESSIONID', $encodedUserData, time() + 3600, '/', null, false, true));
        $response->headers->setCookie(new Cookie('MYSESSIONID', $sessionId, time() + 3600)); 
        return $response;
    }
}
