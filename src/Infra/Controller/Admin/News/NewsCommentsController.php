<?php

declare(strict_types=1);

namespace App\Infra\Controller\Admin\News;

use App\Domain\Entity\News\News;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class NewsCommentsController extends AbstractController
{
    public function __invoke(News $news): Response
    {
        return $this->render('admin/news/comments.html.twig', [
            'news' => $news,
            'comments' => $news->getComments(),
        ]);
    }
}
