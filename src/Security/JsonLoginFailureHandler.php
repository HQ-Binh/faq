<?php
// src/Security/JsonLoginFailureHandler.php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class JsonLoginFailureHandler implements AuthenticationFailureHandlerInterface
{
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        // Tăng số lần đăng nhập không thành công
        $attempts = $request->getSession()->get('login_attempts', 0);
        $request->getSession()->set('login_attempts', $attempts + 1);
var_dump($attempts);
        if ($attempts >= 3) {
            return new JsonResponse(['error' => 'Too many login attempts, please try again later.'], JsonResponse::HTTP_TOO_MANY_REQUESTS);
        }
        return new JsonResponse(['error' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
    }
}
