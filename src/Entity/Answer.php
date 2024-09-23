<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity]
#[ApiResource]
class Answer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    #[ORM\OneToOne(inversedBy: 'answer', targetEntity: Question::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Question $question = null;

    // Getter và Setter cho $id
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter và Setter cho $content
    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    // Getter và Setter cho $question
    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }
}
