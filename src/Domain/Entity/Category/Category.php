<?php

declare(strict_types=1);

namespace App\Domain\Entity\Category;

use App\Domain\Entity\News\News;
use App\Domain\Model\CategoryId;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Category
{
    private readonly DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;
    private ?DateTimeImmutable $deletedAt = null;

    /** @var Collection<int, News> */
    private Collection $news;

    public function __construct(
        private readonly CategoryId $id,
        private string $title,
    ) {
        $this->createdAt = new DateTimeImmutable();
        $this->news      = new ArrayCollection();
    }

    public function getId(): CategoryId
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function onPreUpdate(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function archive(): void
    {
        $this->deletedAt = new DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->title;
    }

    /** @return News[] */
    public function getNews(): array
    {
        return $this->news->toArray();
    }
}
