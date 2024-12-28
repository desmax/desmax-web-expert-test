<?php

declare(strict_types=1);

namespace App\Infra\Controller;

use App\App\News\NewsRepositoryInterface;
use App\Domain\Entity\Category\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function ceil;
use function max;

class CategoryShowController extends AbstractController
{
    public function __invoke(Category $category, NewsRepositoryInterface $newsRepository, Request $request): Response
    {
        $page  = max(1, $request->query->getInt('page', 10));
        $limit = 1;

        $news = $newsRepository->findByCategory(
            category: $category,
            page: $page,
            limit: $limit
        );

        $total = $newsRepository->getTotalByCategory($category);

        return $this->render('news/category.html.twig', [
            'category' => $category,
            'news' => $news,
            'currentPage' => $page,
            'lastPage' => ceil($total / $limit),
        ]);
    }
}
