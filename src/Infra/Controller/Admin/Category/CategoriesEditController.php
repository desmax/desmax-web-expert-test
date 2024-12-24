<?php

declare(strict_types=1);

namespace App\Infra\Controller\Admin\Category;

use App\Domain\Entity\Category\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoriesEditController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function __invoke(Category $category, Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');

            if ($title) {
                $category->setTitle($title);
                $this->em->flush();

                $this->addFlash('success', 'Category updated successfully.');

                return $this->redirectToRoute('app_admin_category_list');
            }
        }

        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
        ]);
    }
}
