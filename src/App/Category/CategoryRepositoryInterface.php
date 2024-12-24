<?php

declare(strict_types=1);

namespace App\App\Category;

use App\Domain\Entity\Category\Category;
use App\Domain\Model\CategoryId;

interface CategoryRepositoryInterface
{
    public function getById(CategoryId $id): Category;

    /** @return Category[] */
    public function getList(int $limit, int $offset): array;

    public function archive(Category $category): void;
}
