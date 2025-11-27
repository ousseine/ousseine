<?php

namespace App\Controller\Main;

use App\Entity\Category;
use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BlogController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $posts
    ){
    }

    #[Route('/blog', name: 'blog', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('blog/index.html.twig', [
            'posts' => $this->posts->findAllByPublishedAt(),
            'title' => 'Blog',
            'description' => 'Blog'
        ]);
    }

    #[Route('/blog/categorie/{slug}', name: 'blog.category', methods: ['GET'])]
    public function category(
        #[MapEntity(mapping: ['slug' => 'slug'])] Category $category
    ): Response
    {
        return $this->render('blog/index.html.twig', [
            'posts' => $this->posts->findByCategory($category),
            'title' => $category->getName(),
            'description' => ''
        ]);
    }

    #[Route('/blog/{slug}', name: 'blog.post', methods: ['GET'])]
    public function show(
        #[MapEntity(mapping: ['slug' => 'slug'])] Post $post
    ): Response
    {
        return $this->render('blog/post.html.twig', [
            'post' => $post,
            'title' => $post->getTitle(),
        ]);
    }

    #[Route('/blog/recherche', name: 'blog.search', methods: ['GET'])]
    public function search(string $q): Response
    {
        return $this->render('blog/index.html.twig', [
            'posts' => '',
            'title' => 'Résultats de la recherche pour : ' . $q,
            'description' => ''
        ]);
    }
}
