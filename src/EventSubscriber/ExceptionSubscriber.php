<?php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        // Đăng ký sự kiện kernel.exception
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

   

    public function onKernelException(ExceptionEvent $event): void
    {
        // Lấy ngoại lệ từ sự kiện
        $exception = $event->getThrowable();

        // Kiểm tra nếu ngoại lệ là NotFoundHttpException
        if ($exception instanceof NotFoundHttpException) {
            $response = new Response();
            $response->setContent(json_encode(['error' => $exception->getMessage()]));
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $response->headers->set('Content-Type', 'application/json');

            $event->setResponse($response);
        }
    }
}
