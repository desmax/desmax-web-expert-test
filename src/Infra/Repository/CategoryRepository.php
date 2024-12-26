<?php

declare(strict_types=1);

namespace App\Infra\Repository;

use App\App\Category\CategoryRepositoryInterface;
use App\App\Exception\NotFound;
use App\Domain\Entity\Category\Category;
use App\Domain\Entity\News\News;
use App\Domain\Model\CategoryId;
use App\Domain\Model\NewsId;
use App\Infra\Model\CategoryId as CategoryIdImpl;
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

    private function getLatestNewsSubquery(int $limit): string
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('newsSub.id')
            ->from(News::class, 'newsSub')
            ->join('newsSub.categories', 'categoriesSub')
            ->andWhere('categoriesSub = category')
            ->andWhere('newsSub.deletedAt IS NULL')
            ->andWhere('categoriesSub.deletedAt IS NULL')
            ->orderBy('newsSub.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getDQL();
    }

    /**
     * @return array<array{
     *     id: CategoryId,
     *     title: string,
     *     news_id: NewsId|null,
     *     news_title: string|null,
     *     shortDescription: string|null,
     *     picture: string|null,
     * }>
     */
    private function getCategoriesWithNewsQuery(string $newsSubquery): array
    {
        /** @var array<array{
         *     id: CategoryId,
         *     title: string,
         *     news_id: NewsId|null,
         *     news_title: string|null,
         *     shortDescription: string|null,
         *     picture: string|null,
         * }> */
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('category.id, category.title, news.id as news_id, news.title as news_title, news.shortDescription, news.picture')
            ->from(Category::class, 'category')
            ->leftJoin('category.news', 'news', 'WITH', "news.id IN ($newsSubquery)")
            ->andWhere('category.deletedAt IS NULL')
            ->orderBy('category.title', 'ASC')
            ->addOrderBy('news.createdAt', 'DESC')
            ->getQuery()
            ->getArrayResult();
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
            $categoryId = $row['id']->value;
            if (! array_key_exists($categoryId, $categorized)) {
                $categorized[$categoryId] = [
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'news' => [],
                ];
            }

            if ($row['news_id'] !== null) {
                $categorized[$categoryId]['news'][] = [
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
        $newsSubquery = $this->getLatestNewsSubquery($newsLimit);
        $results      = $this->getCategoriesWithNewsQuery($newsSubquery);

        return $this->transformQueryResults($results);
    }
}
