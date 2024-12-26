<?php

declare(strict_types=1);

namespace App\Infra\Controller;

use App\App\Category\CategoryRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class FeedController extends AbstractController
{
    public function __invoke(CategoryRepositoryInterface $newsRepository): Response
    {
        $groupedNews = $newsRepository->listCategoriesWithLatestNews(3);

        return $this->render('feed/index.html.twig', ['categories' => $groupedNews]);
    }
}
