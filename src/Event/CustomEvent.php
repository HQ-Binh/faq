<?php
namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class CustomEvent extends Event
{
    public const NAME = 'custom.event';
    private ?string $responseContent = null;

    private string $message;

    public function __construct(string $message)
    {
        // $this->message = $message . " event";
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return  " 1chuoi63";
    }
    public function setResponseContent(string $content): void
    {
        $this->responseContent = $content;
    }
    public function getResponseContent(): ?string
    {
        return $this->responseContent;
    }
}
