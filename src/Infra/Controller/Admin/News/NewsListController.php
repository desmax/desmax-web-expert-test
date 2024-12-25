<?php

declare(strict_types=1);

namespace App\Infra\Controller\Admin\News;

use App\App\News\NewsRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NewsListController extends AbstractController
{
    public function __construct(private NewsRepositoryInterface $newsRepository)
    {
    }

    public function __invoke(Request $request): Response
    {
        $offset = $request->query->getInt('offset');
        $limit  = $request->query->getInt('limit', 10);

        $news = $this->newsRepository->getList($limit, $offset);

        return $this->render('admin/news/list.html.twig', ['news' => $news]);
    }
}
