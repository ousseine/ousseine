<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

#[Route('/category', name: 'category.')]
final class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository $categories,
        private readonly Breadcrumbs $breadcrumbs
    ){
    }

    #[Route(name: 'index', methods: ['GET'])]
    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        $this->breadcrumbs->addItem('Categories');

        return $this->render('admin/category/index.html.twig', [
            'categories' => $this->categories->findAll(),
            'title' => 'Catégories',
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    #[Route('/{slug}/update', name: 'update', methods: ['GET', 'POST'])]
    public function form(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])] ?Category $category = null
    ): Response
    {
        $this->breadcrumbs->addRouteItem('Categories', 'admin.category.index');
        if (null === $category) {
            $category = new Category();
            $this->breadcrumbs->addItem('Nouveau');
        } else {
            $this->breadcrumbs->addItem('Éditer');
        }
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$category->getId()) $this->categories->create($category);
            else $this->categories->update();

            return $this->redirectToRoute('admin.category.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/category/form.html.twig', [
            'category' => $category,
            'form' => $form,
            'title' => $category->getId() ? 'Éditer la catégorie' : 'Créer une catégorie',
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->getPayload()->getString('_token'))) {
            $this->categories->delete($category);
        }

        return $this->redirectToRoute('admin.category.index', [], Response::HTTP_SEE_OTHER);
    }
}
