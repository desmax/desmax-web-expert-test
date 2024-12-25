<?php

declare(strict_types=1);

namespace App\Infra\Repository;

use App\App\Exception\NotFound;
use App\App\News\NewsRepositoryInterface;
use App\Domain\Entity\News\News;
use App\Domain\Model\NewsId;
use App\Infra\Model\NewsId as NewsIdImpl;
use Doctrine\Persistence\ManagerRegistry;

use function sprintf;

/** @extends BaseRepository<News> */
class NewsRepository extends BaseRepository implements NewsRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    public function getById(NewsId $id): News
    {
        $news = $this->find($id);

        if ($news === null || $news->getDeletedAt() !== null) {
            throw new NotFound(sprintf('News with ID "%s" not found.', $id));
        }

        return $news;
    }

    public function getList(int $limit, int $offset): array
    {
        return $this->findBy(['deletedAt' => null], [], $limit, $offset);
    }

    public function archive(News $news): void
    {
        $news->archive();
        $this->getEntityManager()->flush();
    }

    protected function convertStringToEntityId(string $id): NewsId
    {
        return new NewsIdImpl($id);
    }
}
