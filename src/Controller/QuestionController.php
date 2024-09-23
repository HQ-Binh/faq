<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use App\Repository\QuestionRepository;

class QuestionController extends AbstractController
{
    private $entityManager;
    private QuestionRepository $questionRepository;

    public function __construct(EntityManagerInterface $entityManager,QuestionRepository $questionRepository)
    {
        $this->entityManager = $entityManager;
        $this->questionRepository = $questionRepository;
    }

    #[Route('/api/questions', name: 'question_index', methods: ['GET'])]
    public function getAllQuestions(): JsonResponse
    {
        $questions = $this->entityManager->getRepository(Question::class)->findAll();
        $data = [];

        foreach ($questions as $question) {
            $data[] = [
                'id' => $question->getId(),
                'content' => $question->getContent(),
                'category' => $question->getCategory()->getId(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/api/questions-sql', name: 'question_index', methods: ['GET'])]
public function getAllQuestionsSql(): JsonResponse
{
    $sql = "SELECT q.id, q.content, c.id AS category_id
    FROM question q
    JOIN category c ON q.category_id = c.id";

$connection = $this->entityManager->getConnection();
$result = $connection->executeQuery($sql); 

$questions = $result->fetchAllAssociative(); 

return new JsonResponse($questions);
}


    #[Route('/api/questions/{id}', name: 'question_show', methods: ['GET'])]
    public function getQuestion(int $id): JsonResponse
    {
        $question = $this->entityManager->getRepository(Question::class)->find($id);

        if (!$question) {
            return new JsonResponse(['message' => 'Question not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $question->getId(),
            'content' => $question->getContent(),
            'category' => $question->getCategory()->getId(),
        ];

        return new JsonResponse($data);
    }

    #[Route('/api/questions', name: 'question_create', methods: ['POST'])]
    public function createQuestion(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['content']) || empty($data['category'])) {
            return new JsonResponse(['message' => 'Content and category ID are required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $question = new Question();
        $question->setContent($data['content']);

        $category = $this->entityManager->getRepository(Category::class)->find($data['category']);
        if (!$category) {
            return new JsonResponse(['message' => 'Category not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $question->setCategory($category);

        $this->entityManager->persist($question);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Question created'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/api/questions/{id}', name: 'question_update', methods: ['PUT'])]
    public function updateQuestion(int $id, Request $request): JsonResponse
    {
        $question = $this->entityManager->getRepository(Question::class)->find($id);

        if (!$question) {
            return new JsonResponse(['message' => 'Question not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (empty($data['content'])) {
            return new JsonResponse(['message' => 'Content is required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $question->setContent($data['content']);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Question updated'], JsonResponse::HTTP_OK);
    }

    #[Route('/api/questions/{id}', name: 'question_delete', methods: ['DELETE'])]
    public function deleteQuestion(int $id): JsonResponse
    {
        $question = $this->entityManager->getRepository(Question::class)->find($id);

        if (!$question) {
            return new JsonResponse(['message' => 'Question not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($question);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Question deleted'], JsonResponse::HTTP_OK);
    }

//lay chi tiet cau hoi bang MapEnity
    #[Route('/api/question/{id}', name: 'question_show2', methods: ['GET'])]
    public function show(
        #[MapEntity(mapping: ['id' => 'id'])] Question $question //lay id truyen len map voi id table question
    ): JsonResponse {
        // Chuyển đổi câu hỏi sang dạng mảng JSON
        $data = [
            'id' => $question->getId(),
            'content' => $question->getContent(),
            'answers' => []
        ];

        // Lấy tất cả các câu trả lời cho câu hỏi
        foreach ($question->getAnswer() as $answer) {
            $data['answers'][] = [
                'id' => $answer->getId(),
                'content' => $answer->getContent(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/api/categories/{category_id}/questions/{question_id}', name: 'question_show3', methods: ['GET'])]
    public function showQuestion(
        #[MapEntity(mapping: ['category_id' => 'id'], message: 'The category does not exist')]
        Category $category,
        #[MapEntity(mapping: ['question_id' => 'id'])]
        Question $question
    ): JsonResponse {
        // Kiểm tra xem câu hỏi có thuộc danh mục không
        if ($question->getCategory() !== $category) {
            return new JsonResponse(['message' => 'Question does not belong to this category'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Trả về thông tin của câu hỏi
        $data = [
            'id' => $question->getId(),
            'content' => $question->getContent(),
        ];

        return new JsonResponse($data);
    }

    #[Route('/api/questions/search/{keyword}', name: 'search_questions', methods: ['GET'])]
    public function searchQuestions(string $keyword): JsonResponse
    {
        $questions = $this->questionRepository->findAllContainingContent($keyword);

        $data = [];

        foreach ($questions as $question) {
            $data[] = [
                'id' => $question->getId(),
                'content' => $question->getContent(),
            ];
        }

        return new JsonResponse($data);
    }
}
