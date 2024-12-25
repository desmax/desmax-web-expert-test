<?php

declare(strict_types=1);

namespace App\Domain\Entity\News;

use App\Domain\Entity\User\User;
use App\Domain\Model\CommentId;
use DateTimeImmutable;

class Comment
{
    private readonly DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(
        private readonly CommentId $id,
        private News $news,
        private readonly User $author,
        private string $content,
    ) {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): CommentId
    {
        return $this->id;
    }

    public function getNews(): News
    {
        return $this->news;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
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
