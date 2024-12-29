<?php

declare(strict_types=1);

namespace App\Infra\Controller\Admin\Category;

use App\App\Category\CategoryRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoriesListController extends AbstractController
{
    public function __construct(private CategoryRepositoryInterface $categoryRepository)
    {
    }

    public function __invoke(Request $request): Response
    {
        $offset = $request->query->getInt('offset');
        $limit  = $request->query->getInt('limit', 50);

        $categories = $this->categoryRepository->getList($limit, $offset);

        return $this->render('admin/category/list.html.twig', ['categories' => $categories]);
    }
}
