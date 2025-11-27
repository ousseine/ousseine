<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

#[Route('/post', name: 'post.')]
final class PostController extends AdminController
{
    public function __construct(
        private readonly PostRepository $posts,
        private readonly Breadcrumbs $breadcrumbs
    ){
    }

    #[Route(name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $this->breadcrumbs->addItem('Posts');

        return $this->render('admin/post/index.html.twig', [
            'posts' => $this->posts->findBy([], ['createdAt' => 'DESC']),
            'title' => 'Posts',
            'name' => 'Posts',
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    #[Route('/{slug}/update', name: 'update', methods: ['GET', 'POST'])]
    public function form(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])] ?Post $post = null
    ): Response
    {
        $this->breadcrumbs->addRouteItem('Posts', 'admin.post.index');
        if (!$post) {
            $post = new Post();
            $this->breadcrumbs->addItem('Nouveau');
        } else {
            $this->breadcrumbs->addItem('Éditer');
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$post->getId()) {
                $post->setOwner($this->getUser());
                $this->posts->create($post);
            }
            else {
                $this->posts->update();
            }

            return $this->redirectToRoute('admin.post.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/post/form.html.twig', [
            'post' => $post,
            'form' => $form,
            'title' => $post->getId() ? 'Éditer le post' : 'Créer un post',
            'name' => $post->getId() ? 'Post update' : 'Post new'
        ]);
    }

    #[Route('/{slug}', name: 'preview', methods: ['GET'])]
    public function preview(
        #[MapEntity(mapping: ['slug' => 'slug'])] Post $post
    ): Response
    {
        $this->breadcrumbs->addRouteItem('Posts', 'admin.post.index');
        $this->breadcrumbs->addItem('Preview');

        return $this->render('admin/post/preview.html.twig', [
            'post' => $post,
            'title' => $post->getTitle(),
            'name' => 'Post preview'
        ]);
    }

    #[Route('/{id}/status', name: 'status', methods: ['GET', 'POST'])]
    public function status(Post $post): Response
    {
        if ($post->isStatus()) {
            $post->setStatus(false);
            $post->setPublishedAt(null);
        } else {
            $post->setStatus(true);
            $post->setPublishedAt(new \DateTimeImmutable());
        }

        $this->posts->update();

        return $this->redirectToRoute('admin.post.index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request, Post $post): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->getPayload()->getString('_token'))) {
            $this->posts->delete($post);
        }

        return $this->redirectToRoute('admin.post.index', [], Response::HTTP_SEE_OTHER);
    }
}
