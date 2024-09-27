<?php
namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\AnswerRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use App\Security\AuthenticationService;

#[Route('/api/answers')]
class AnswerController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private $authenticationService;

    public function __construct(EntityManagerInterface $entityManager,AuthenticationService $authenticationService)
    {
        $this->entityManager = $entityManager;
        $this->authenticationService = $authenticationService;
    }

    #[Route('', name: 'answer_index', methods: ['GET'])]
    public function getAllAnswers(): JsonResponse
    {
        $answers = $this->entityManager->getRepository(Answer::class)->findAll();
        $data = [];

        foreach ($answers as $answer) {
            $data[] = [
                'id' => $answer->getId(),
                'content' => $answer->getContent(),
                'question' => $answer->getQuestion()->getId(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('Id/{id}', name: 'answer_show', methods: ['GET'])]
    public function getAnswer(int $id): JsonResponse
    {
        $answer = $this->entityManager->getRepository(Answer::class)->find($id);

        if (!$answer) {
            return new JsonResponse(['message' => 'Answer not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $answer->getId(),
            'content' => $answer->getContent(),
            'question' => $answer->getQuestion()->getId(),
        ];

        return new JsonResponse($data);
    }

    #[Route('', name: 'answer_create', methods: ['POST'])]
    public function createAnswer(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $answer = new Answer();
        $answer->setContent($data['content']);
        $answer->setQuestion($this->entityManager->getRepository(Question::class)->find($data['question']));

        $this->entityManager->persist($answer);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Answer created successfully', 'id' => $answer->getId()], JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'answer_update', methods: ['PUT'])]
    public function updateAnswer(Request $request, int $id): JsonResponse
    {
        $answer = $this->entityManager->getRepository(Answer::class)->find($id);

        if (!$answer) {
            return new JsonResponse(['message' => 'Answer not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $answer->setContent($data['content']);

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Answer updated successfully']);
    }

    #[Route('/{id}', name: 'answer_delete', methods: ['DELETE'])]
    public function deleteAnswer(int $id): JsonResponse
    {
        $answer = $this->entityManager->getRepository(Answer::class)->find($id);

        if (!$answer) {
            return new JsonResponse(['message' => 'Answer not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($answer);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Answer deleted successfully']);
    }

    #[Route('/api/answers/search-by-question', name: 'answer_search_by_question_content', methods: ['GET'])]
public function searchAnswerByQuestionContent(Request $request): JsonResponse
{
    $content = $request->query->get('content');

    if (!$content) {
        return new JsonResponse(['message' => 'Question content is required'], JsonResponse::HTTP_BAD_REQUEST);
    }

    // Tìm câu hỏi theo nội dung
    $questions = $this->entityManager->getRepository(Question::class)->createQueryBuilder('q')
        ->where('q.content LIKE :content')
        ->setParameter('content', '%' . $content . '%')
        ->getQuery()
        ->getResult();

    if (!$questions) {
        return new JsonResponse(['message' => 'No questions found for the given content'], JsonResponse::HTTP_NOT_FOUND);
    }

    $data = [];

    // Lấy câu trả lời cho từng câu hỏi tìm được
    foreach ($questions as $question) {
        $answers = $question->getAnswer(); // Giả sử bạn có phương thức getAnswer() trong entity Question

        $answersData = [];
        foreach ($answers as $answer) {
            $answersData[] = [
                'id' => $answer->getId(),
                'content' => $answer->getContent(),
            ];
        }

        $data[] = [
            'question_id' => $question->getId(),
            'question_content' => $question->getContent(),
            'answers' => $answersData,
            'answer' => $answers->getContent(),
        ];
    }

    return new JsonResponse($data);
}

#[Route('/api/answer/{answer_id}', name: 'answer_show', methods: ['GET'])]
public function show(
    #[MapEntity(expr: 'repository.find(answer_id)')] Answer $answer
): JsonResponse {
    $data = [
        'id' => $answer->getId(),
        'content' => $answer->getContent(),
    ];

    return new JsonResponse($data);
}

#[Route('/api/security/answers-auth', name: 'api_answers-auth', methods: ['GET'])]
public function getAllAnswersAuth(Request $request): JsonResponse
{
    // Khởi động session nếu chưa bắt đầu
    // if (!$request->getSession()->isStarted()) {
    //     $request->getSession()->start();
    // }

    // // Kiểm tra xem sessionId có hợp lệ không
    // $sessionId = $request->cookies->get('MYSESSIONID'); // Tên cookie bạn đã cấu hình
    // if (!$sessionId) {
    //     return new JsonResponse(['error' => 'Cookie not found'], JsonResponse::HTTP_UNAUTHORIZED);
    // }
    // var_dump($sessionId);

    // if (!$request->getSession()->isStarted()) {
    //     return new JsonResponse(['error' => 'Session not started'], JsonResponse::HTTP_UNAUTHORIZED);
    // }
    $authResponse = $this->authenticationService->authenticate($request);
        if ($authResponse) {
            return $authResponse;
        };
    // // Kiểm tra sessionId
    // if ($sessionId && $request->getSession()->getId() !== $sessionId) {
    //     return new JsonResponse(['error' => 'Invalid session ID'], JsonResponse::HTTP_UNAUTHORIZED);
    // }

    $answers = $this->entityManager->getRepository(Answer::class)->findAll();

    $data = [];
    foreach ($answers as $answer) {
        $data[] = [
            'id' => $answer->getId(),
            'content' => $answer->getContent(), 
            'question' => $answer->getQuestion()->getId(), 
        ];
    }

    return new JsonResponse($data);
}


}
