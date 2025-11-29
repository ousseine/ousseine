<?php

namespace App\Twig\Components;

use App\Repository\CategoryRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('categories')]
readonly class Category
{
    public function __construct(
        private CategoryRepository $categories
    ){
    }

    public function getCategories(): array
    {
        return $this->categories->findAll();
    }
}