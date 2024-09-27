<?php
// src/Controller/AuthController.php

// src/Controller/AuthController.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    private $authenticationUtils;

    public function __construct(AuthenticationUtils $authenticationUtils)
    {
        $this->authenticationUtils = $authenticationUtils;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        
        return new JsonResponse(['status' => 'Request received, processing...'], JsonResponse::HTTP_OK);
    }

    // #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    // public function logout(): JsonResponse
    // {
    //     return new JsonResponse(['status' => 'User logged out!'], JsonResponse::HTTP_OK);
    // }
    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(Request $request): JsonResponse
    {
        // Lấy session từ request
        $session = $request->getSession();
        
        // Hủy session hiện tại
        $session->invalidate(); // Hủy session để đăng xuất

        return new JsonResponse(['message' => 'Successfully logged out'], JsonResponse::HTTP_OK);
    }

}

