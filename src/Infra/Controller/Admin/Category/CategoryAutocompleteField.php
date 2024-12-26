<?php

declare(strict_types=1);

namespace App\Infra\Controller\Admin\Category;

use App\Domain\Entity\Category\Category;
use App\Infra\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

/**
 * @template TData of array<Category>
 * @extends AbstractType<TData>
 */
#[AsEntityAutocompleteField]
class CategoryAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Category::class,
            'placeholder' => 'Choose a Category',
            'choice_label' => 'title',
            'searchable_fields' => ['title'],
            'security' => 'ROLE_ADMIN',
            'multiple' => true,
            'query_builder' => static function (CategoryRepository $repository) {
                return $repository->createQueryBuilder('c')
                    ->andWhere('c.deletedAt IS NULL');
            },
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
