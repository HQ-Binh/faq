<?php
namespace App\DTO;

class AnswerDto
{
    public ?int $id = null;
    public ?string $content = null;

    public function __construct(?int $id, ?string $content)
    {
        $this->id = $id;
        $this->content = $content;
    }
}
