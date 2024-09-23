<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class BlogController extends AbstractController
{
   
    
    #[Route('/lucky/number')]
    public function number(): Response
    {
        $number = random_int(0, 100);

        return $this->render('lucky/number.html.twig', [
            'number' => $number,
        ]);
    }
    #[Route(
        '/posts/{id}',
        name: 'post_show',
        // expressions can retrieve route parameter values using the "params" variable
        // condition: "params['id'] < 1000"
    )]
    public function showPost(int $id): Response
    {
        if ($id >= 1000) {
            return new JsonResponse(['error' => 'Invalid ID'], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['id' => $id], Response::HTTP_OK);
    }
    #[Route('/blog/{page<\d+>}', name: 'blog_list')]
    public function list(int $page=1): Response
    {
        return new JsonResponse(['id' => $page], Response::HTTP_OK);
    }

    #[Route(
        path: '/articles/{_locale}/search.{_format}',
        locale: 'en',
        format: 'html',
        requirements: [
            '_locale' => 'en|fr',
            '_format' => 'html|xml',
        ],
    )]
    public function search(string $_locale, string $_format): Response
    {
        // Render the template with the locale and format
        return $this->render('articles/search.html.twig', [
            'locale' => $_locale,
            'format' => $_format,
        ]);
    }
    #[Route('/blog-title/{page}', name: 'blog_index', defaults: ['page' => 1, 'title' => 'Hello world!'])]
public function index(int $page, string $title): Response
{
    // Print the page and title
    return new Response(
        '<html><body>Page: ' . $page . '<br>Title: ' . $title . '</body></html>'
    );
}


}