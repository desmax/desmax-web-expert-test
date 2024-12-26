<?php

declare(strict_types=1);

namespace App\Infra\Repository;

use App\App\Exception\NotFound;
use App\App\News\NewsRepositoryInterface;
use App\Domain\Entity\Category\Category;
use App\Domain\Entity\News\News;
use App\Domain\Model\NewsId;
use App\Infra\Model\NewsId as NewsIdImpl;
use DateTimeImmutable;
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
        return $this->findBy(['deletedAt' => null], ['createdAt' => 'DESC'], $limit, $offset);
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

    public function findByCategory(Category $category, int $page = 1, int $limit = 10): array
    {
        return $this->createQueryBuilder('n')
            ->join('n.categories', 'c')
            ->andWhere('c = :category')
            ->setParameter('category', $category)
            ->orderBy('n.createdAt', 'DESC')
            ->andWhere('n.deletedAt IS NULL')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getTotalByCategory(Category $category): int
    {
        return (int) $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->join('n.categories', 'c')
            ->andWhere('c = :category')
            ->setParameter('category', $category)
            ->andWhere('n.deletedAt IS NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTopNewsByComments(DateTimeImmutable $from, DateTimeImmutable $to, int $limit): array
    {
        return $this->createQueryBuilder('n')
            ->select('n as news', 'COUNT(c.id) as commentCount')
            ->leftJoin('n.comments', 'c')
            ->andWhere('c.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->groupBy('n.id')
            ->orderBy('commentCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
