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

class JsonLoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger; // Khởi tạo logger
    }
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        // Lấy thông tin người dùng từ token
        $user = $token->getUser();
        // $this->logger->info('Before invalidation: session id = ' . $request->getSession()->getId());
        // Hủy session cũ
        //  if ($request->getSession()) {
            //     $request->getSession()->invalidate();
            // }
            
            // Khởi tạo một session mới
            $request->getSession()->start();
            
            // Lấy session ID mới
            $sessionId = $request->getSession()->getId();
            var_dump($sessionId);
        
        // Tạo phản hồi JSON với thông tin người dùng (có thể tùy chỉnh)
         $response= new JsonResponse([
            'status' => 'User logged in successfully!',
            'sessionId' => $sessionId,
            'email' => $user->getUserIdentifier(),  // lấy email
            'roles' => $user->getRoles(),            // Lấy quyền của người dùng
        ], JsonResponse::HTTP_OK);
        $response->headers->setCookie(new Cookie('MYSESSIONID', $sessionId, time() + 3600)); 
        return $response;
    }
}
