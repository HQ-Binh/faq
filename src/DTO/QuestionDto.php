<?php
namespace App\DTO;

class QuestionDto
{
    public ?int $id = null;
    public ?string $content = null;
    public ?int $category_id = null;

    public function __construct(?int $id, ?string $content, ?int $categoryId)
    {
        $this->id = $id;
        $this->content = $content;
        $this->category_id = $categoryId;
    }
}
