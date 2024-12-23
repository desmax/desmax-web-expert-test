<?php

declare(strict_types=1);

namespace App\Infra\Repository;

use App\App\Category\CategoryRepositoryInterface;
use App\App\Exception\NotFound;
use App\Domain\Entity\Category\Category;
use App\Domain\Model\CategoryId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use function sprintf;

/** @extends ServiceEntityRepository<Category> */
class CategoryRepository extends ServiceEntityRepository implements CategoryRepositoryInterface
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
}
