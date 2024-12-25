<?php

declare(strict_types=1);

namespace App\Infra\Repository;

use App\App\Exception\NotFound;
use App\App\News\CommentRepositoryInterface;
use App\Domain\Entity\News\Comment;
use App\Domain\Model\CommentId;
use App\Infra\Model\CommentId as CommentIdImpl;
use Doctrine\Persistence\ManagerRegistry;

use function sprintf;

/** @extends BaseRepository<Comment> */
class CommentRepository extends BaseRepository implements CommentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function getById(CommentId $id): Comment
    {
        $comment = $this->find($id);

        if ($comment === null || $comment->getDeletedAt() !== null) {
            throw new NotFound(sprintf('Comment with ID "%s" not found.', $id));
        }

        return $comment;
    }

    public function getList(int $limit, int $offset): array
    {
        return $this->findBy(['deletedAt' => null], ['createdAt' => 'DESC'], $limit, $offset);
    }

    public function archive(Comment $comment): void
    {
        $comment->archive();
        $this->getEntityManager()->flush();
    }

    protected function convertStringToEntityId(string $id): CommentId
    {
        return new CommentIdImpl($id);
    }
}
