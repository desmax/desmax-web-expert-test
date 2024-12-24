<?php

declare(strict_types=1);

namespace App\Infra\Controller\Admin\Category;

use App\Domain\Entity\Category\Category;
use App\Infra\Model\CategoryId;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoriesCreateController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function __invoke(Request $request): Response
    {
        // TODO add form validation
        if ($request->isMethod('POST')) {
            $title = $request->request->getString('title');

            if ($title !== '') {
                $category = new Category(
                    new CategoryId(),
                    $title
                );

                $this->em->persist($category);
                $this->em->flush();

                $this->addFlash('success', 'Category created successfully.');

                return $this->redirectToRoute('app_admin_category_list');
            }
        }

        return $this->render('admin/category/create.html.twig');
    }
}
