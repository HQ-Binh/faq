<?php
// src/Controller/TestExceptionController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestExceptionController
{
    #[Route('/test-exception', name: 'test_exception')]
    public function testException(): Response
    {
        // Ném ra một ngoại lệ để kiểm tra Event Subscriber
        throw new \Exception("This is a test exception!");
    }

    #[Route('/not-found', name: 'not_found')]
    public function notFound(): Response
    {
        // Ném ra một ngoại lệ không tìm thấy
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Resource not found!");
    }
}
