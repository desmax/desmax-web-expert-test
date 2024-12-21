<?php

declare(strict_types=1);

namespace App\Domain\Entity\Category;

use App\Domain\Model\CategoryId;
use DateTimeImmutable;

class Category
{
    private readonly DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(
        private readonly CategoryId $id,
        private string $title,
    ) {
        $this->createdAt = new DateTimeImmutable();
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
}
