<?php

declare(strict_types=1);

namespace App\Infra\Controller\Admin\News;

use App\App\News\CommentRepositoryInterface;
use App\Domain\Entity\News\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NewsCommentArchiveController extends AbstractController
{
    public function __construct(private CommentRepositoryInterface $commentRepository)
    {
    }

    public function __invoke(Comment $comment, Request $request): Response
    {
        $this->commentRepository->archive($comment);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(status: Response::HTTP_NO_CONTENT);
        }

        $this->addFlash('success', 'Comment archived successfully.');

        return $this->redirectToRoute('app_admin_news_comments', ['news' => $comment->getNews()->getId()]);
    }
}
