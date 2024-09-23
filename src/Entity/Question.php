<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\OneToOne(mappedBy: 'question', targetEntity: Answer::class, cascade: ['persist', 'remove'])]
    private ?Answer $answer = null;

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

    // Getter và Setter cho $category
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    // Getter và Setter cho $answer
    public function getAnswer(): ?Answer
    {
        return $this->answer;
    }

    public function setAnswer(?Answer $answer): self
    {
        // Set the owning side of the relation if necessary
        if ($answer && $answer->getQuestion() !== $this) {
            $answer->setQuestion($this);
        }

        $this->answer = $answer;

        return $this;
    }
}
