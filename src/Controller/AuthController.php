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

    #[Route('/api/profile', name: 'api_profile')]
    public function profileUser(): Response
    {
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Call whatever methods you've added to your User class
        // For example, if you added a getFirstName() method, you can use that.
        return new Response('Well hi there '.$user->getEmail());
    }
}

