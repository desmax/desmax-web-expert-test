<?php

declare(strict_types=1);

namespace App\Infra\Controller\Admin\Category;

use App\Domain\Entity\Category\Category;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryDTO
{
    public function __construct(
        #[Assert\NotBlank(message: 'Title cannot be empty')]
        #[Assert\Length(
            min: 3,
            max: 255,
            minMessage: 'Title must be at least {{ limit }} characters long',
            maxMessage: 'Title cannot be longer than {{ limit }} characters',
        )]
        public string $title = '',
    ) {
    }

    public static function fromCategory(Category $category): self
    {
        return new self(
            title: $category->getTitle(),
        );
    }
}
