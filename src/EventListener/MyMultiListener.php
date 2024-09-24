<?php
namespace App\EventListener;

use App\Event\CustomEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\JsonResponse;

#[AsEventListener(event: CustomEvent::class, method: 'onCustomEvent')]
#[AsEventListener(event: 'foo',method: 'onFoo', priority: 42)]
#[AsEventListener(event: 'bar', method: 'onBarEvent')]
final class MyMultiListener
{
    public function onCustomEvent(CustomEvent $event): void
    {
        // Logic xử lý cho CustomEvent
        $message = $event->getMessage();
        $response = new Response();
        $response->setContent($message);
        $responseContent = "Custom Event Triggered with message: $message";
        
        
        $event->setResponseContent($responseContent);
        
        error_log("Custom Event Triggered with message: $message");
    }

    public function onFoo(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        // Lấy thông tin từ yêu cầu
        $request = $event->getRequest();
        $method = $request->getMethod();
        $path = $request->getPathInfo();

        // Ghi log thông tin yêu cầu
        error_log("Received $method request for $path");

        // Thêm một tiêu đề tùy chỉnh vào phản hồi
        $response = new Response();
        $response->headers->set('X-Custom-Header', 'CustomValue');

        // Thiết lập phản hồi vào sự kiện
        if ($request->getPathInfo() == '/trigger-foo-event') {
            $response = new JsonResponse(['message' => 'Response from foo event']);
            $event->setResponse($response);
        }
        // dd($event);
        error_log("Foo Event Triggered");
    }

    public function onBarEvent(): void
    {
        // Logic xử lý cho Bar Event
        error_log("Bar Event Triggered");
    }
}
