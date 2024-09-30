<?php
// src/Security/JsonLoginSuccessHandler.php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class JsonLoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $logger;
    private $tokenStorage;
    private $jwtManager;
    public function __construct(LoggerInterface $logger,TokenStorageInterface $tokenStorage, JWTTokenManagerInterface $jwtManager)
    {
        $this->logger = $logger; // Khởi tạo logger
        $this->tokenStorage = $tokenStorage;
        $this->jwtManager = $jwtManager;
    }
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
{
    // Lấy thông tin người dùng từ token
    $user = $token->getUser();

    // Khởi tạo một session mới nếu chưa bắt đầu
    if (!$request->getSession()->isStarted()) {
        $request->getSession()->start();
    }

    // Lấy session ID mới
    

    // Tạo JWT token
    $jwt = $this->jwtManager->create($user); // Tạo token từ thông tin người dùng
    $sessionId = $request->getSession()->getId();
    // Mã hóa thông tin người dùng
    $userData = [
        'email' => $user->getUserIdentifier(),
        'roles' => $user->getRoles(),
        'sessionId' => $sessionId,
        'jwt' => $jwt,
    ];

    // Lưu thông tin người dùng vào session
    $request->getSession()->set('sess_data', $userData); // Lưu vào database

    // JSON encode và mã hóa base64 để lưu vào cookie
    $encodedUserData = base64_encode(json_encode($userData));

    // Đặt token vào TokenStorage
    // $this->tokenStorage->setToken($token);

    // Tạo phản hồi JSON với thông tin người dùng
    $response = new JsonResponse([
        'status' => 'User logged in successfully!',
        'sessionId' => $sessionId,
        'email' => $user->getUserIdentifier(),  // Identifier da cau61 hinh2 la2 email
        'roles' => $user->getRoles(),   
        'jwt' => $jwt,         
    ], JsonResponse::HTTP_OK);

    // Đặt cookie chứa thông tin người dùng đã mã hóa
    $response->headers->setCookie(new Cookie('USERDATA', $encodedUserData, time() + 3600, '/', null, false, true));
    $response->headers->setCookie(new Cookie('MYSESSIONID', $sessionId, time() + 3600)); 

    return $response;
}

}
