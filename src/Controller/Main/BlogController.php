<?php

namespace App\Controller\Main;

use App\Entity\Category;
use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/blog')]
final class BlogController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $posts
    ){
    }

    #[Route(name: 'blog', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('blog/index.html.twig', [
            'posts' => $this->posts->findAllByPublishedAt($page),
            'title' => 'Blog',
            'description' => 'Un brin de code, une pincée de maths, une dose de tech, et une envie d\'explorer pour nourrir la curiosité.'
        ]);
    }

    #[Route('/categorie/{slug}', name: 'blog.category', methods: ['GET'])]
    public function category(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])] Category $category
    ): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('blog/index.html.twig', [
            'posts' => $this->posts->findByCategory($category, $page),
            'title' => $category->getName(),
            'description' => ''
        ]);
    }

    #[Route('/{slug}', name: 'blog.post', methods: ['GET'])]
    public function show(
        #[MapEntity(mapping: ['slug' => 'slug'])] Post $post
    ): Response
    {
        return $this->render('blog/post.html.twig', [
            'post' => $post,
            'title' => $post->getTitle(),
        ]);
    }

    #[Route('/recherche', name: 'blog.search', methods: ['GET'], priority: 2)]
    public function search(Request $request): Response
    {
        $q = $request->query->get('q');
        $posts = $this->posts->findBySearch($q);

        return $this->render('blog/search.html.twig', [
            'posts' => $posts,
            'title' => 'Rechercher un article',
            'query' => $q
        ]);
    }
}
