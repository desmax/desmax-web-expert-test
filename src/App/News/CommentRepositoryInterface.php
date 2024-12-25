<?php

declare(strict_types=1);

namespace App\App\News;

use App\Domain\Entity\News\Comment;
use App\Domain\Model\CommentId;

interface CommentRepositoryInterface
{
    public function getById(CommentId $id): Comment;

    /** @return Comment[] */
    public function getList(int $limit, int $offset): array;

    public function archive(Comment $comment): void;
}
