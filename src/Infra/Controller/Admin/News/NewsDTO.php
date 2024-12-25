<?php

declare(strict_types=1);

namespace App\Infra\Controller\Admin\News;

use App\Domain\Entity\Category\Category;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class NewsDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    public string $title = '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    public string $shortDescription = '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    public string $content = '';

    /** @var array<Category> */
    #[Assert\NotBlank]
    #[Assert\Count(min: 1, minMessage: 'Select at least one category')]
    public array $categories = [];

    #[Assert\File(
        maxSize: '2M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
        maxSizeMessage: 'The file is too large. Maximum size is 2MB.',
        mimeTypesMessage: 'Please upload a valid image file (JPEG, PNG, WEBP)'
    )]
    public ?UploadedFile $picture = null;
}
