<?php
namespace App\Controller;

use App\Event\CustomEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\Event;

class EventController extends AbstractController
{
    private EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    
    #[Route('/trigger-custom-event', name: 'trigger_custom_event')]
    public function triggerCustomEvent(): Response
    {
        $event = new CustomEvent("This is a test event!");
        $this->dispatcher->dispatch($event);

        // Nhận phản hồi từ sự kiện
        $responseContent = $event->getResponseContent() ?? 'No response from event';

        // Trả về phản hồi
        return new Response($responseContent);
    }

    #[Route('/trigger-foo-event', name: 'trigger_foo_event', methods: ['POST'])]
    public function triggerFooEvent(): Response
    {
        $this->dispatcher->dispatch(new Event(), 'foo');

        return new Response('Foo event triggered!', Response::HTTP_OK);
    }

    #[Route('/trigger-bar-event', name: 'trigger_bar_event', methods: ['POST'])]
    public function triggerBarEvent(): Response
    {
        $this->dispatcher->dispatch(new Event(), 'bar');

        return new Response('Bar event triggered!', Response::HTTP_OK);
    }
}
