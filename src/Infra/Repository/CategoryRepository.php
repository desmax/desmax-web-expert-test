<?php

declare(strict_types=1);

namespace App\Infra\Repository;

use App\App\Category\CategoryRepositoryInterface;
use App\App\Exception\NotFound;
use App\Domain\Entity\Category\Category;
use App\Domain\Model\CategoryId;
use App\Infra\Model\CategoryId as CategoryIdImpl;
use Doctrine\Persistence\ManagerRegistry;

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

        if ($category === null) {
            throw new NotFound(sprintf('Category with ID "%s" not found.', $id));
        }

        return $category;
    }

    public function getList(int $limit, int $offset): array
    {
        return $this->findBy([], [], $limit, $offset);
    }

    protected function convertStringToEntityId(string $id): CategoryId
    {
        return new CategoryIdImpl($id);
    }
}
