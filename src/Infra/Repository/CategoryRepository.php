<?php

declare(strict_types=1);

namespace App\Infra\Repository;

use App\App\Category\CategoryRepositoryInterface;
use App\App\Exception\NotFound;
use App\Domain\Entity\Category\Category;
use App\Domain\Model\CategoryId;
use App\Domain\Model\NewsId;
use App\Infra\Model\CategoryId as CategoryIdImpl;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

use function array_filter;
use function array_key_exists;
use function array_values;
use function count;
use function sprintf;

/** @extends BaseRepository<Category> */
class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function getById(CategoryId $id): Category
    {
        $category = $this->find($id);

        if ($category === null || $category->getDeletedAt() !== null) {
            throw new NotFound(sprintf('Category with ID "%s" not found.', $id));
        }

        return $category;
    }

    public function getList(int $limit, int $offset): array
    {
        return $this->findBy(['deletedAt' => null], ['createdAt' => 'DESC'], $limit, $offset);
    }

    public function archive(Category $category): void
    {
        $category->archive();
        $this->getEntityManager()->flush();
    }

    protected function convertStringToEntityId(string $id): CategoryId
    {
        return new CategoryIdImpl($id);
    }

    /** @return array<array{id: CategoryId, title: string, news_id: NewsId|null, news_title: string|null, shortDescription: string|null, picture: string|null,}> */
    private function getCategoriesWithNewsQuery(int $limit): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id', 'category_id')
            ->addScalarResult('title', 'title')
            ->addScalarResult('news_id', 'news_id', 'news_id')
            ->addScalarResult('news_title', 'news_title')
            ->addScalarResult('short_description', 'shortDescription')
            ->addScalarResult('picture', 'picture');

        $sql = '
        SELECT
            category.id,
            category.title,
            latest_news.id as news_id,
            latest_news.title as news_title,
            latest_news.short_description,
            latest_news.picture,
            latest_news.created_at
        FROM categories category
        LEFT JOIN LATERAL (
            SELECT
                news.id,
                news.title,
                news.short_description,
                news.picture,
                news.created_at
            FROM news
            JOIN news_categories nc ON nc.news_id = news.id
            WHERE
                nc.category_id = category.id
                AND news.deleted_at IS NULL
            ORDER BY news.created_at DESC
            LIMIT :limit
        ) latest_news ON true
        WHERE category.deleted_at IS NULL
        ORDER BY category.title ASC, latest_news.created_at DESC
    ';

        $query = $this->getEntityManager()
            ->createNativeQuery($sql, $rsm)
            ->setParameter('limit', $limit);

        /** @var array<array{id: CategoryId, title: string, news_id: NewsId|null, news_title: string|null, shortDescription: string|null, picture: string|null,}> */
        return $query->getArrayResult();
    }

    /**
     * @param array<array{id: CategoryId, title: string, news_id: NewsId|null, news_title: string|null, shortDescription: string|null, picture: string|null,}> $results
     *
     * @return array<array{
     *     id: CategoryId,
     *     title: string,
     *     news: array{
     *         id: NewsId,
     *         title: string,
     *         shortDescription: string,
     *         picture: string|null,
     *     }[],
     * }>
     */
    private function transformQueryResults(array $results): array
    {
        $categorized = [];
        foreach ($results as $row) {
            $categoryIdString = (string) $row['id'];
            if (! array_key_exists($categoryIdString, $categorized)) {
                $categorized[$categoryIdString] = [
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'news' => [],
                ];
            }

            if ($row['news_id'] !== null) {
                $categorized[$categoryIdString]['news'][] = [
                    'id' => $row['news_id'],
                    'title' => (string) $row['news_title'],
                    'shortDescription' => (string) $row['shortDescription'],
                    'picture' => $row['picture'],
                ];
            }
        }

        $categorizedWithNoEmptyCategories = array_filter($categorized, static fn ($category) => count($category['news']) > 0);

        return array_values($categorizedWithNoEmptyCategories);
    }

    public function listCategoriesWithLatestNews(int $newsLimit): array
    {
        $results = $this->getCategoriesWithNewsQuery($newsLimit);

        return $this->transformQueryResults($results);
    }
}
