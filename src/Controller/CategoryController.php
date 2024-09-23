<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

class CategoryController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // #[Route('/api/categories', name: 'category_index', methods: ['GET'])]
    // public function getAllCategories(): JsonResponse
    // {
    //     $categories = $this->entityManager->getRepository(Category::class)->findAll();
    //     $data = [];

    //     foreach ($categories as $category) {
    //         $data[] = [
    //             'id' => $category->getId(),
    //             'name' => $category->getName(),
    //             'question'=>$category->getQuestions(),
    //         ];
    //     }

    //     return new JsonResponse($data);
    // }
    #[Route('/api/categories', name: 'category_index', methods: ['GET'])]
public function getAllCategories(): JsonResponse
{
    $categories = $this->entityManager->getRepository(Category::class)->findAll();
    $data = [];

    foreach ($categories as $category) {
        $questionsData = [];
        foreach ($category->getQuestions() as $question) {
            $questionsData[] = [
                'id' => $question->getId(),
                'content' => $question->getContent(),
            ];
        }

        $data[] = [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'questions' => $questionsData, // Thay đổi ở đây
        ];
    }

    return new JsonResponse($data);
}

    #[Route('/api/categories/{id}', name: 'category_show', methods: ['GET'])]
    public function getCategory(int $id): JsonResponse
    {
        $category = $this->entityManager->getRepository(Category::class)->find($id);

        if (!$category) {
            return new JsonResponse(['message' => 'Category not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'question'=>$category->getQuestions(),
        ];
       

        return new JsonResponse($data);
    }

    #[Route('/api/categories', name: 'category_create', methods: ['POST'])]
    public function createCategory(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name'])) {
            return new JsonResponse(['message' => 'Name is required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $category = new Category();
        $category->setName($data['name']);

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Category created'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/api/categories/{id}', name: 'category_update', methods: ['PUT'])]
    public function updateCategory(int $id, Request $request): JsonResponse
    {
        $category = $this->entityManager->getRepository(Category::class)->find($id);

        if (!$category) {
            return new JsonResponse(['message' => 'Category not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (empty($data['name'])) {
            return new JsonResponse(['message' => 'Name is required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $category->setName($data['name']);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Category updated'], JsonResponse::HTTP_OK);
    }

    #[Route('/api/categories/{id}', name: 'category_delete', methods: ['DELETE'])]
    public function deleteCategory(int $id): JsonResponse
    {
        $category = $this->entityManager->getRepository(Category::class)->find($id);

        if (!$category) {
            return new JsonResponse(['message' => 'Category not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Category deleted'], JsonResponse::HTTP_NO_CONTENT);
    }

    // #[Route('/api/questions_by_category/{category}', name: 'questions_by_category', methods: ['GET'])]
    // public function questionsByCategory(
    //     #[MapEntity(expr: 'repository.findBy({"category": category}, {}, 10)')]
    //     iterable $questions
    // ): JsonResponse {
    //     $data = [];

    //     // Duyệt qua tất cả các câu hỏi và lấy dữ liệu
    //     foreach ($questions as $question) {
    //         $data[] = [
    //             'id' => $question->getId(),
    //             'content' => $question->getContent(),
    //             'created_at' => $question->getCreatedAt(),
    //         ];
    //     }

    //     return new JsonResponse($data);
    // }

    #[Route('/api/questions_by_category/{category_id}', name: 'questions_by_category', methods: ['GET'])]
    public function questionsByCategory(int $category_id): JsonResponse
    {
        // Tìm danh mục theo category_id
        $category = $this->entityManager->getRepository(Category::class)->find($category_id);

        if (!$category) {
            return new JsonResponse(['message' => 'Category not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Lấy danh sách câu hỏi thuộc danh mục này
        $questions = $this->entityManager->getRepository(Question::class)->findBy(['category' => $category]);

        $data = [];

        // Duyệt qua tất cả các câu hỏi và lấy dữ liệu
        foreach ($questions as $question) {
            $data[] = [
                'id' => $question->getId(),
                'content' => $question->getContent(),
            ];
        }

        return new JsonResponse($data);
    }
}
