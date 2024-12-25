<?php

declare(strict_types=1);

namespace App\Infra\Controller\Admin\News;

use App\Domain\Entity\Category\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @extends AbstractType<NewsDTO> */
class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['empty_data' => ''])
            ->add('shortDescription', TextareaType::class, ['empty_data' => ''])
            ->add('content', TextareaType::class, ['empty_data' => ''])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'autocomplete' => true,
                'multiple' => true,
                'required' => true,
                'choice_label' => 'title',
            ])
            ->add('picture', FileType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NewsDTO::class,
            'csrf_protection' => true,
        ]);
    }
}
