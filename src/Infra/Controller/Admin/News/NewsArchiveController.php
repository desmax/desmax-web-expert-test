<?php

declare(strict_types=1);

namespace App\Infra\Controller\Admin\News;

use App\App\News\NewsRepositoryInterface;
use App\Domain\Entity\News\News;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NewsArchiveController extends AbstractController
{
    public function __construct(private NewsRepositoryInterface $newsRepository)
    {
    }

    public function __invoke(News $news, Request $request): Response
    {
        $this->newsRepository->archive($news);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(status: Response::HTTP_NO_CONTENT);
        }

        $this->addFlash('success', 'News archived successfully.');

        return $this->redirectToRoute('app_admin_news_list');
    }
}
