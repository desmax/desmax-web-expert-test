<?php

declare(strict_types=1);

namespace App\App\News;

use App\Domain\Entity\Category\Category;
use App\Domain\Entity\News\News;
use App\Domain\Model\NewsId;

interface NewsRepositoryInterface
{
    public function getById(NewsId $id): News;

    /** @return News[] */
    public function getList(int $limit, int $offset): array;

    public function archive(News $news): void;

    /** @return News[] */
    public function findByCategory(Category $category, int $page = 1, int $limit = 10): array;

    public function getTotalByCategory(Category $category): int;
}
