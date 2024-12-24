<?php

declare(strict_types=1);

namespace App\Infra\Controller\Admin\Category;

use App\App\Category\CategoryRepositoryInterface;
use App\Domain\Entity\Category\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class CategoriesArchiveController extends AbstractController
{
    public function __construct(private CategoryRepositoryInterface $categoryRepository)
    {
    }

    public function __invoke(Category $category): Response
    {
        $this->categoryRepository->archive($category);

        $this->addFlash('success', 'Category deleted successfully.');

        return $this->redirectToRoute('app_admin_category_list');
    }
}
