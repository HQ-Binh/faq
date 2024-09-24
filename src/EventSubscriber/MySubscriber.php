<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MySubscriber implements EventSubscriberInterface
{
    public function onKernelEventsREQUEST($event): void
    {
        // ...
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'KernelEvents::REQUEST' => 'onKernelEventsREQUEST',
        ];
    }
}
